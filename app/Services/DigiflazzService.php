<?php

namespace App\Services;

use App\Models\TopupCallback;
use App\Models\TopupTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigiflazzService
{
    protected string $username;
    protected string $key;
    protected string $baseUrl;
    protected bool $isTesting;

    public function __construct()
    {
        $this->username = config('digiflazz.username', '');
        $this->key = config('digiflazz.key', '');
        $this->baseUrl = config('digiflazz.base_url', 'https://api.digiflazz.com/v1');
        $this->isTesting = config('digiflazz.mode', 'development') === 'development';
    }

    /**
     * Cek Saldo Akun Digiflazz
     */
    public function checkBalance(): array
    {
        $sign = md5($this->username . $this->key . 'depo');

        try {
            $response = Http::timeout(15)->post("{$this->baseUrl}/cek-saldo", [
                'cmd' => 'deposit',
                'username' => $this->username,
                'sign' => $sign,
            ]);

            return $response->json() ?? ['data' => ['rc' => '99', 'message' => 'Invalid JSON Response']];
        } catch (\Throwable $e) {
            Log::error('Digiflazz checkBalance Error: ' . $e->getMessage());
            return ['data' => ['rc' => '99', 'message' => $e->getMessage()]];
        }
    }

    /**
     * Ambil Daftar Harga & Produk Digiflazz (Prepaid)
     */
    public function getPriceList(): array
    {
        $sign = md5($this->username . $this->key . 'pricelist');

        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}/price-list", [
                'cmd' => 'prepaid',
                'username' => $this->username,
                'sign' => $sign,
            ]);

            return $response->json() ?? ['data' => []];
        } catch (\Throwable $e) {
            Log::error('Digiflazz getPriceList Error: ' . $e->getMessage());
            return ['data' => [], 'error' => $e->getMessage()];
        }
    }

    /**
     * Inquiry Pascaprabayar (PLN, PDAM, BPJS, Cek Nama)
     */
    public function inquiryPasca(string $refId, string $buyerSkuCode, string $customerNo): array
    {
        $sign = md5($this->username . $this->key . $refId);

        $payload = [
            'commands' => 'inq-pasca',
            'username' => $this->username,
            'buyer_sku_code' => $buyerSkuCode,
            'customer_no' => $customerNo,
            'ref_id' => $refId,
            'sign' => $sign,
            'testing' => $this->isTesting,
        ];

        try {
            $response = Http::timeout(20)->post("{$this->baseUrl}/transaction", $payload);
            return $response->json() ?? ['data' => ['rc' => '99', 'status' => 'Gagal', 'message' => 'Invalid Response']];
        } catch (\Throwable $e) {
            Log::error("Digiflazz inquiryPasca Error [{$refId}]: " . $e->getMessage());
            return ['data' => ['rc' => '99', 'status' => 'Gagal', 'message' => $e->getMessage()]];
        }
    }

    /**
     * Kirim Order Transaksi Top-Up ke Digiflazz
     *
     * @param string $refId ID unik pesanan topup kita (e.g. TPU-20260815-00001)
     * @param string $buyerSkuCode Kode SKU Produk Digiflazz (e.g. ML86)
     * @param string $customerNo ID Akun Tujuan Game (e.g. 123456781234)
     * @param int|null $maxPrice Opsional batas harga maksimum
     */
    public function createTransaction(string $refId, string $buyerSkuCode, string $customerNo, ?int $maxPrice = null): array
    {
        $sign = md5($this->username . $this->key . $refId);

        $payload = [
            'username' => $this->username,
            'buyer_sku_code' => $buyerSkuCode,
            'customer_no' => $customerNo,
            'ref_id' => $refId,
            'sign' => $sign,
            'testing' => $this->isTesting,
        ];

        if ($maxPrice !== null) {
            $payload['max_price'] = $maxPrice;
        }

        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}/transaction", $payload);
            $result = $response->json();

            Log::info("Digiflazz Transaction Request [{$refId}]", [
                'payload' => $payload,
                'response' => $result,
            ]);

            return $result ?? ['data' => ['rc' => '99', 'status' => 'Gagal', 'message' => 'Invalid Response']];
        } catch (\Throwable $e) {
            Log::error("Digiflazz createTransaction Error [{$refId}]: " . $e->getMessage());
            return [
                'data' => [
                    'rc' => '99',
                    'status' => 'Gagal',
                    'message' => $e->getMessage(),
                ]
            ];
        }
    }

    /**
     * Handle Webhook Callback dari Digiflazz
     */
    public function handleWebhook(array $payload): ?TopupTransaction
    {
        $data = $payload['data'] ?? $payload;
        $refId = $data['ref_id'] ?? null;

        if (!$refId) {
            Log::warning('Digiflazz Webhook received without ref_id', ['payload' => $payload]);
            return null;
        }

        $transaction = TopupTransaction::where('ref_id', $refId)->first();

        // Simpan log callback
        TopupCallback::create([
            'topup_transaction_id' => $transaction?->id,
            'event' => 'digiflazz.webhook',
            'payload' => $payload,
            'processed_at' => now(),
        ]);

        if ($transaction) {
            $statusStr = strtolower($data['status'] ?? 'pending');
            $normalizedStatus = match ($statusStr) {
                'sukses', 'success' => 'success',
                'gagal', 'failed' => 'failed',
                default => 'processing',
            };

            $transaction->update([
                'status' => $normalizedStatus,
                'response_code' => $data['rc'] ?? $transaction->response_code,
                'message' => $data['message'] ?? $transaction->message,
                'serial_number' => $data['sn'] ?? $transaction->serial_number,
                'completed_at' => in_array($normalizedStatus, ['success', 'failed']) ? now() : null,
            ]);

            // Jika item ini bagian dari order, periksa apakah seluruh order sudah completed
            $orderItem = $transaction->orderItem;
            if ($orderItem && $orderItem->order) {
                $order = $orderItem->order;
                if ($normalizedStatus === 'success') {
                    $order->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]);
                } elseif ($normalizedStatus === 'failed') {
                    $order->update([
                        'status' => 'failed',
                    ]);
                }
            }
        }

        return $transaction;
    }
}
