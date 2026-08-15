<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;
    protected string $snapApiUrl;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key', '');
        $this->clientKey = config('midtrans.client_key', '');
        $this->isProduction = config('midtrans.is_production', false);
        $this->snapApiUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Buat Snap Token & Pembayaran untuk Order
     */
    public function createSnapTransaction(Order $order): Payment
    {
        $order->loadMissing(['items.product', 'user']);

        $itemDetails = [];
        foreach ($order->items as $item) {
            $itemDetails[] = [
                'id' => (string)$item->product_id,
                'price' => (int)$item->unit_price,
                'quantity' => $item->quantity,
                'name' => mb_substr($item->product->name ?? 'Game Product', 0, 50),
            ];
        }

        // Jika ada diskon
        if ($order->discount > 0) {
            $itemDetails[] = [
                'id' => 'DISCOUNT',
                'price' => -(int)$order->discount,
                'quantity' => 1,
                'name' => 'Diskon Promo',
            ];
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $order->order_number . '-' . time(), // unik di midtrans
                'gross_amount' => (int)$order->total,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $order->user?->name ?? 'Customer GameNexa',
                'email' => $order->customer_email ?? 'customer@gamenexa.com',
                'phone' => $order->customer_phone,
            ],
            'callbacks' => [
                'finish' => url('/order/' . $order->order_number),
            ],
            'expiry' => [
                'unit' => 'minutes',
                'duration' => 60, // Expire dalam 1 jam
            ],
        ];

        $snapToken = null;
        $redirectUrl = null;

        // Hanya kirim request ke Midtrans jika server_key sudah diisi di .env
        if (!empty($this->serverKey)) {
            try {
                $authHeader = 'Basic ' . base64_encode($this->serverKey . ':');
                $response = Http::withHeaders([
                    'Authorization' => $authHeader,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->post($this->snapApiUrl, $payload);

                $result = $response->json();
                $snapToken = $result['token'] ?? null;
                $redirectUrl = $result['redirect_url'] ?? null;
            } catch (\Throwable $e) {
                Log::error('Midtrans createSnapTransaction Exception: ' . $e->getMessage());
            }
        }

        // Simpan data payment
        return Payment::create([
            'order_id' => $order->id,
            'provider' => 'midtrans',
            'transaction_id' => $payload['transaction_details']['order_id'],
            'gross_amount' => $order->total,
            'transaction_status' => 'pending',
            'snap_token' => $snapToken,
            'checkout_url' => $redirectUrl,
            'expired_at' => now()->addHour(),
        ]);
    }

    /**
     * Handle Notifikasi Webhook dari Midtrans
     */
    public function handleNotification(array $payload): ?Payment
    {
        $midtransOrderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $serverKey = $this->serverKey;

        // Verifikasi Signature Key Midtrans jika server key disetel
        if (!empty($serverKey) && isset($payload['signature_key'])) {
            $expectedSignature = hash('sha512', $midtransOrderId . $statusCode . $grossAmount . $serverKey);
            if ($payload['signature_key'] !== $expectedSignature) {
                Log::warning('Midtrans Notification Invalid Signature', ['payload' => $payload]);
                return null;
            }
        }

        $payment = Payment::where('transaction_id', $midtransOrderId)->first();

        // Simpan log notifikasi
        PaymentLog::create([
            'payment_id' => $payment?->id,
            'event' => 'midtrans.notification',
            'payload' => $payload,
            'processed_at' => now(),
        ]);

        if (!$payment) {
            Log::warning('Midtrans payment not found for order_id: ' . $midtransOrderId);
            return null;
        }

        $transactionStatus = $payload['transaction_status'] ?? 'pending';
        $fraudStatus = $payload['fraud_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? null;

        $isSuccess = false;
        $isFailed = false;

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $isSuccess = true;
            }
        } elseif ($transactionStatus === 'settlement') {
            $isSuccess = true;
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $isFailed = true;
        }

        $payment->update([
            'payment_type' => $paymentType,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
            'paid_at' => $isSuccess ? now() : $payment->paid_at,
        ]);

        $order = $payment->order;
        if ($order) {
            if ($isSuccess && $order->status === 'unpaid') {
                $order->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                // Otomatis kirim topup ke Digiflazz setelah pembayaran terkonfirmasi!
                app(OrderService::class)->processTopupAfterPayment($order);
            } elseif ($isFailed) {
                $order->update([
                    'status' => $transactionStatus === 'expire' ? 'expired' : 'failed',
                ]);
            }
        }

        return $payment;
    }
}
