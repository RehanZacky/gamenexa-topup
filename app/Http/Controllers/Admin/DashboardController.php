<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard Utama Admin
     */
    public function index(): View
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $totalCategories = Category::count();
        
        $totalOrders = Order::count();
        $pendingOrders = Order::whereIn('status', ['unpaid', 'paid', 'processing'])->count();
        $successOrders = Order::where('status', 'completed')->count();
        $failedOrders = Order::whereIn('status', ['failed', 'expired', 'cancelled'])->count();

        $totalRevenue = Order::where('status', 'completed')->sum('total');

        // Hitung estimasi profit dari transaksi sukses berdasarkan (selling_price - modal_price)
        $estimatedProfit = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', 'completed')
            ->selectRaw('SUM((order_items.unit_price - products.modal_price) * order_items.quantity) as profit')
            ->value('profit') ?? 0;

        // Transaksi Terbaru
        $recentOrders = Order::with(['items.product.category', 'payments', 'items.topupTransaction'])
            ->latest()
            ->take(8)
            ->get();

        // Ringkasan Kategori & Produk
        $categoriesSummary = Category::withCount(['products', 'products as active_products_count' => function ($q) {
            $q->where('status', 'active');
        }])->orderBy('products_count', 'desc')->take(6)->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'activeProducts',
            'totalCategories',
            'totalOrders',
            'pendingOrders',
            'successOrders',
            'failedOrders',
            'totalRevenue',
            'estimatedProfit',
            'recentOrders',
            'categoriesSummary'
        ));
    }
}
