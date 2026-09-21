<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Tampilkan Daftar Pesanan Customer
     */
    public function index(Request $request): View
    {
        $query = Order::with(['items.product.category', 'payments', 'items.topupTransaction']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        $statuses = ['unpaid', 'paid', 'processing', 'completed', 'failed', 'expired'];

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    /**
     * Tampilkan Detail Pesanan
     */
    public function show(Order $order): View
    {
        $order->load(['items.product.category', 'payments.logs', 'items.topupTransaction.callbacks', 'user']);

        return view('admin.orders.show', compact('order'));
    }
}
