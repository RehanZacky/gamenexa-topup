<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Halaman Order Game (Pilih nominal, input ID, pilih bayar)
     */
    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)
            ->where('status', 'active')
            ->with(['products' => function ($q) {
                $q->where('status', 'active')->orderBy('sort_order', 'asc');
            }])
            ->firstOrFail();

        return view('pages.order', compact('category'));
    }

    /**
     * Submit Pesanan & Dapatkan Snap Token Midtrans
     */
    public function store(Request $request, OrderService $orderService, \App\Services\AccountValidatorService $validator)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_number' => 'required|string|max:50',
            'zone_id' => 'nullable|string|max:50',
            'server' => 'nullable|string|max:50',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:100',
        ], [
            'product_id.required' => 'Silakan pilih nominal produk top-up.',
            'customer_number.required' => 'ID Akun game / Nomor Tujuan wajib diisi.',
            'customer_phone.required' => 'Nomor WhatsApp wajib diisi untuk notifikasi.',
        ]);

        $product = \App\Models\Product::with('category')->findOrFail($validated['product_id']);
        $check = $validator->validate($product->category->slug, $validated['customer_number'], $validated['zone_id'] ?? null);

        if (!$check['success']) {
            return back()->withErrors(['customer_number' => $check['message']])->withInput();
        }

        $order = $orderService->createOrder($validated);

        return redirect()->route('order.invoice', ['order_number' => $order->order_number])
            ->with('success', 'Pesanan berhasil dibuat! Silakan selesaikan pembayaran.');
    }

    /**
     * Halaman Invoice / Cek Status Transaksi
     */
    public function invoice(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.product.category', 'payments', 'items.topupTransaction'])
            ->firstOrFail();

        $payment = $order->latestPayment;

        return view('pages.invoice', compact('order', 'payment'));
    }

    /**
     * Halaman Pelacakan Pesanan Berdasarkan Nomor Invoice / No WhatsApp
     */
    public function tracking(Request $request)
    {
        $query = $request->input('search');
        $orders = collect();

        if ($query) {
            $orders = Order::where('order_number', 'LIKE', "%{$query}%")
                ->orWhere('customer_phone', 'LIKE', "%{$query}%")
                ->with(['items.product.category', 'items.topupTransaction'])
                ->latest()
                ->take(10)
                ->get();
        }

        return view('pages.tracking', compact('orders', 'query'));
    }
}
