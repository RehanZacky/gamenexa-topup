<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TopupTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TopupTransactionController extends Controller
{
    /**
     * Tampilkan Riwayat Transaksi Topup Digiflazz
     */
    public function index(Request $request): View
    {
        $query = TopupTransaction::with(['orderItem.order', 'orderItem.product.category', 'callbacks']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ref_id', 'like', "%{$search}%")
                  ->orWhere('provider_product_code', 'like', "%{$search}%")
                  ->orWhere('customer_number', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $transactions = $query->latest()->paginate(20)->withQueryString();

        return view('admin.topup_transactions.index', compact('transactions'));
    }
}
