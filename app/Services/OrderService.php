<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\TopupTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Buat Order Baru dan Inisiasi Pembayaran Midtrans
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $product = Product::with('category')->findOrFail($data['product_id']);

            $quantity = (int)($data['quantity'] ?? 1);
            $unitPrice = $product->selling_price;
            $subtotal = $unitPrice * $quantity;
            $discount = 0; // Untuk voucher nanti
            $total = $subtotal - $discount;

            $orderNumber = 'GNX-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $data['user_id'] ?? null,
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'status' => 'unpaid',
                'expired_at' => now()->addHour(),
            ]);

            $targetData = [];
            if (!empty($data['zone_id'])) {
                $targetData['zone_id'] = $data['zone_id'];
            }
            if (!empty($data['server'])) {
                $targetData['server'] = $data['server'];
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'customer_number' => $data['customer_number'], // User ID Game
                'target_data' => $targetData,
            ]);

            // Inisiasi Pembayaran Midtrans Snap
            app(MidtransService::class)->createSnapTransaction($order);

            return $order->load(['items.product.category', 'payments']);
        });
    }

    /**
     * Eksekusi Pengiriman Top-Up ke Digiflazz setelah Pembayaran Terkonfirmasi Sukses
     */
    public function processTopupAfterPayment(Order $order): void
    {
        $order->loadMissing('items.product');

        foreach ($order->items as $item) {
            $product = $item->product;
            if (!$product) {
                continue;
            }

            // Gabungkan user_id dan zone_id jika game memerlukan zone_id (seperti MLBB: 123456781234)
            $customerNo = $item->customer_number;
            if (!empty($item->target_data['zone_id'])) {
                $customerNo .= $item->target_data['zone_id'];
            }

            $refId = 'TPU-' . date('Ymd') . '-' . strtoupper(Str::random(8));

            // Catat transaksi topup di database
            $topupTx = TopupTransaction::create([
                'order_item_id' => $item->id,
                'provider' => 'digiflazz',
                'ref_id' => $refId,
                'provider_product_code' => $product->provider_product_code,
                'customer_number' => $customerNo,
                'price' => $product->modal_price,
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            // Kirim request ke Digiflazz API
            $digiflazz = app(DigiflazzService::class);
            $response = $digiflazz->createTransaction(
                $refId,
                $product->provider_product_code,
                $customerNo
            );

            $data = $response['data'] ?? [];
            $statusStr = strtolower($data['status'] ?? 'pending');

            $status = match ($statusStr) {
                'sukses', 'success' => 'success',
                'gagal', 'failed' => 'failed',
                default => 'processing',
            };

            $topupTx->update([
                'status' => $status,
                'response_code' => $data['rc'] ?? null,
                'message' => $data['message'] ?? 'Permintaan top-up telah dikirim ke Digiflazz',
                'serial_number' => $data['sn'] ?? null,
                'completed_at' => in_array($status, ['success', 'failed']) ? now() : null,
            ]);

            // Update status order
            if ($status === 'success') {
                $order->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            } elseif ($status === 'processing') {
                $order->update([
                    'status' => 'processing',
                ]);
            } elseif ($status === 'failed') {
                $order->update([
                    'status' => 'failed',
                ]);
            }
        }
    }
}
