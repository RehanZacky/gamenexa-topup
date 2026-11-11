<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class DoitService
{
    private string $apiKey;
    private string $baseUrl;
    private string $signingSecret;

    public function __construct()
    {
        $this->apiKey = (string) config('duitku.api_key', '');
        $this->baseUrl = rtrim((string) config('duitku.base_url', 'https://pay.doit.id'), '/');
        $this->signingSecret = (string) config('duitku.signing_secret', '');
    }

    public function createPayment(Order $order): Payment
    {
        $existing = $order->payments()
            ->where('provider', 'doit')
            ->where('transaction_status', 'pending')
            ->latest()
            ->first();

        if ($existing && (!$existing->expired_at || $existing->expired_at->isFuture())) {
            return $existing;
        }

        if ($this->apiKey === '') {
            throw new RuntimeException('DUITKU_API_KEY belum dikonfigurasi.');
        }

        $expiresIn = 3600;
        $payload = [
            'amount' => (int) $order->total,
            'reference' => $order->order_number,
            'rail' => 'any',
            'expires_in' => $expiresIn,
            'return_url' => config('duitku.return_url') ?: route('order.invoice', $order->order_number),
            'customer' => [
                'name' => $order->user?->name ?? 'Customer GameNexa',
                'phone' => $order->customer_phone,
                'email' => $order->customer_email,
            ],
            'metadata' => ['order_number' => $order->order_number],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->withHeaders([
            'Idempotency-Key' => $order->order_number,
        ])->post($this->baseUrl . '/v1/payments', $payload);

        if (!$response->successful()) {
            Log::error('Doit payment creation failed', [
                'order_number' => $order->order_number,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
            throw new RuntimeException('Pembayaran Doit.id gagal dibuat.');
        }

        $result = $response->json();

        return Payment::create([
            'order_id' => $order->id,
            'provider' => 'doit',
            'transaction_id' => $result['id'] ?? null,
            'payment_type' => $result['rail'] ?? null,
            'gross_amount' => $result['amount'] ?? $order->total,
            'transaction_status' => $result['status'] ?? 'pending',
            'checkout_url' => $result['hosted_url'] ?? null,
            'qr_string' => $result['qr_content'] ?? null,
            'va_number' => $result['va_number'] ?? null,
            'bank_name' => $result['va_bank'] ?? null,
            'paid_at' => isset($result['paid_at']) ? $result['paid_at'] : null,
            'expired_at' => $result['expires_at'] ?? now()->addSeconds($expiresIn),
        ]);
    }

    public function getPayment(Payment $payment): array
    {
        if (!$payment->transaction_id || $this->apiKey === '') {
            return [];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->get($this->baseUrl . '/v1/payments/' . rawurlencode($payment->transaction_id));

        if (!$response->successful()) {
            throw new RuntimeException('Status pembayaran Doit.id tidak dapat dibaca.');
        }

        return $response->json();
    }

    public function verifySignature(string $rawBody, ?string $signature): bool
    {
        if ($this->signingSecret === '' || !$signature || !preg_match('/^t=(\d+),v1=([a-f0-9]+)$/i', $signature, $matches)) {
            return false;
        }

        $expected = hash_hmac('sha256', $matches[1] . '.' . $rawBody, $this->signingSecret);

        return hash_equals($expected, $matches[2]);
    }

    public function handleWebhook(array $payload): ?Payment
    {
        $eventId = $payload['id'] ?? null;
        $eventType = $payload['type'] ?? null;
        $data = $payload['data'] ?? [];
        $reference = $data['reference'] ?? null;

        if (!$eventId || !$eventType || !$reference) {
            return null;
        }

        if (PaymentLog::where('external_event_id', $eventId)->exists()) {
            return Payment::where('transaction_id', $data['id'] ?? null)->first();
        }

        $payment = Payment::where('provider', 'doit')
            ->whereHas('order', fn ($query) => $query->where('order_number', $reference))
            ->first();

        if (!$payment) {
            return null;
        }

        PaymentLog::create([
            'payment_id' => $payment->id,
            'external_event_id' => $eventId,
            'event' => 'doit.' . $eventType,
            'payload' => $payload,
            'processed_at' => now(),
        ]);

        $status = $eventType === 'payment.paid' ? 'paid' : ($eventType === 'payment.expired' ? 'expired' : null);
        if (!$status) {
            return $payment;
        }

        $payment->update([
            'transaction_status' => $status,
            'paid_at' => $status === 'paid' ? now() : $payment->paid_at,
        ]);

        $order = $payment->order;
        if ($status === 'paid' && $order && $order->status === 'unpaid') {
            $order->update(['status' => 'paid', 'paid_at' => now()]);
            app(OrderService::class)->processTopupAfterPayment($order);
        } elseif ($status === 'expired' && $order && $order->status === 'unpaid') {
            $order->update(['status' => 'expired']);
        }

        return $payment;
    }
}
