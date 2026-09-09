@extends('layouts.admin')

@section('title', 'Daftar Pesanan Customer — Admin GameNexa')
@section('header-title', 'Daftar Pesanan Customer')
@section('header-subtitle', 'Pantau seluruh invoice, status pembayaran Midtrans, dan pengiriman top-up Digiflazz')

@section('content')
<div class="space-y-6">
    <!-- Search & Filter Bar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[240px]">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice (GNX-...) / no. WhatsApp..."
                       class="w-full bg-dark-800/90 border border-purple-900/30 rounded-xl pl-9 pr-4 py-2.5 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-purple-500">
            </div>

            <select name="status" onchange="this.form.submit()" class="bg-dark-800/90 border border-purple-900/30 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-purple-500">
                <option value="">Semua Status Order</option>
                @foreach($statuses as $st)
                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ strtoupper($st) }}</option>
                @endforeach
            </select>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.orders.index') }}" class="px-3 py-2.5 rounded-xl bg-dark-700 text-zinc-400 hover:text-white text-xs font-semibold flex items-center gap-1.5 border border-purple-900/30">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset</span>
                </a>
            @endif
        </form>
    </div>

    <!-- Orders Table Card -->
    <div class="glass-card rounded-2xl overflow-hidden shadow-xl shadow-black/20">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-purple-900/30 bg-dark-800/80 text-[11px] font-extrabold uppercase tracking-wider text-zinc-400">
                        <th class="py-4 px-5">Nomor Invoice</th>
                        <th class="py-4 px-5">Produk & Game</th>
                        <th class="py-4 px-5">Target Akun</th>
                        <th class="py-4 px-5">Kontak Customer</th>
                        <th class="py-4 px-5 text-right">Total Tagihan</th>
                        <th class="py-4 px-5 text-center">Status Order</th>
                        <th class="py-4 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-900/15 text-xs text-zinc-300">
                    @forelse($orders as $ord)
                        @php
                            $badgeClass = match($ord->status) {
                                'completed' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',
                                'paid', 'processing' => 'bg-purple-500/15 text-purple-300 border-purple-500/30',
                                'unpaid' => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                                default => 'bg-red-500/15 text-red-400 border-red-500/30',
                            };
                            $firstItem = $ord->items->first();
                        @endphp
                        <tr class="hover:bg-purple-950/20 transition-colors">
                            <td class="py-4 px-5">
                                <a href="{{ route('admin.orders.show', $ord) }}" class="font-mono font-bold text-white hover:text-purple-300 text-xs">
                                    {{ $ord->order_number }}
                                </a>
                                <div class="text-[10px] text-zinc-500 mt-0.5">{{ $ord->created_at->format('d M Y, H:i') }} WIB</div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="font-bold text-white text-xs">{{ $firstItem->product->category->name ?? 'Game' }}</div>
                                <div class="text-[11px] text-purple-300">{{ $firstItem->product->name ?? 'Item' }}</div>
                            </td>
                            <td class="py-4 px-5 font-mono">
                                <span class="text-zinc-300">{{ $firstItem->customer_number ?? '—' }}</span>
                                @if(!empty($firstItem->target_data['zone_id']))
                                    <span class="text-purple-400 font-bold">({{ $firstItem->target_data['zone_id'] }})</span>
                                @endif
                            </td>
                            <td class="py-4 px-5">
                                <div class="text-zinc-300 font-semibold">{{ $ord->customer_phone }}</div>
                                @if($ord->customer_email)
                                    <div class="text-[10px] text-zinc-500">{{ $ord->customer_email }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right font-mono font-bold text-white text-sm">
                                Rp {{ number_format($ord->total, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $badgeClass }}">
                                    <span>{{ $ord->status }}</span>
                                </span>
                            </td>
                            <td class="py-4 px-5 text-right">
                                <a href="{{ route('admin.orders.show', $ord) }}" class="px-3 py-1.5 rounded-lg bg-dark-800 hover:bg-purple-600/30 text-purple-400 hover:text-white border border-purple-900/30 text-xs font-bold transition-colors inline-flex items-center gap-1.5">
                                    <span>Detail</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-zinc-500">
                                <i class="fa-solid fa-receipt text-3xl mb-3 block text-zinc-600"></i>
                                <span class="font-semibold text-sm">Tidak ada transaksi pesanan ditemukan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-purple-900/20 bg-dark-800/50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
