@extends('layouts.admin')

@section('title', 'Log Top-up Digiflazz — Admin GameNexa')
@section('header-title', 'Riwayat Top-up Provider (Digiflazz)')
@section('header-subtitle', 'Monitoring transaksi pengiriman H2H, response code, dan Serial Number (SN)')

@section('content')
<div class="space-y-6">
    <!-- Filter -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <form action="{{ route('admin.topup_transactions.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[240px]">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Ref ID / SKU / ID Akun / SN..."
                       class="w-full bg-dark-800/90 border border-purple-900/30 rounded-xl pl-9 pr-4 py-2.5 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-purple-500">
            </div>

            <select name="status" onchange="this.form.submit()" class="bg-dark-800/90 border border-purple-900/30 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-purple-500">
                <option value="">Semua Status Topup</option>
                <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.topup_transactions.index') }}" class="px-3 py-2.5 rounded-xl bg-dark-700 text-zinc-400 hover:text-white text-xs font-semibold flex items-center gap-1.5 border border-purple-900/30">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset</span>
                </a>
            @endif
        </form>
    </div>

    <!-- Topup Transactions Table Card -->
    <div class="glass-card rounded-2xl overflow-hidden shadow-xl shadow-black/20">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-purple-900/30 bg-dark-800/80 text-[11px] font-extrabold uppercase tracking-wider text-zinc-400">
                        <th class="py-4 px-5">Ref ID Transaksi</th>
                        <th class="py-4 px-5">Produk & SKU</th>
                        <th class="py-4 px-5">ID Akun Tujuan</th>
                        <th class="py-4 px-5 text-right">Harga Modal</th>
                        <th class="py-4 px-5 text-center">Status</th>
                        <th class="py-4 px-5">Serial Number / Pesan</th>
                        <th class="py-4 px-5 text-right">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-900/15 text-xs text-zinc-300">
                    @forelse($transactions as $tx)
                        @php
                            $statusClass = match($tx->status) {
                                'success' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',
                                'pending', 'processing' => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                                default => 'bg-red-500/15 text-red-400 border-red-500/30',
                            };
                        @endphp
                        <tr class="hover:bg-purple-950/20 transition-colors">
                            <td class="py-4 px-5 font-mono">
                                <span class="font-bold text-white text-xs">{{ $tx->ref_id }}</span>
                                @if($tx->orderItem && $tx->orderItem->order)
                                    <div class="text-[10px] text-zinc-500 mt-0.5">
                                        <a href="{{ route('admin.orders.show', $tx->orderItem->order) }}" class="text-purple-400 hover:underline">
                                            {{ $tx->orderItem->order->order_number }}
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-5">
                                <div class="font-bold text-white text-xs">{{ $tx->orderItem->product->category->name ?? 'Game' }}</div>
                                <code class="text-[11px] text-purple-300">{{ $tx->provider_product_code }}</code>
                            </td>
                            <td class="py-4 px-5 font-mono text-white">
                                {{ $tx->customer_number }}
                            </td>
                            <td class="py-4 px-5 text-right font-mono text-zinc-400">
                                Rp {{ number_format($tx->price, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $statusClass }}">
                                    {{ $tx->status }}
                                </span>
                            </td>
                            <td class="py-4 px-5 font-mono text-xs">
                                @if($tx->serial_number)
                                    <span class="px-2 py-1 rounded bg-dark-900 border border-emerald-500/40 text-emerald-300 font-bold select-all">
                                        {{ $tx->serial_number }}
                                    </span>
                                @else
                                    <span class="text-zinc-500 text-[11px]">{{ $tx->message ?? '—' }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right text-zinc-400 text-[11px]">
                                {{ $tx->created_at->format('d M, H:i') }} WIB
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-zinc-500">
                                <i class="fa-solid fa-server text-3xl mb-3 block text-zinc-600"></i>
                                <span class="font-semibold text-sm">Tidak ada log transaksi Digiflazz.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-purple-900/20 bg-dark-800/50">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
