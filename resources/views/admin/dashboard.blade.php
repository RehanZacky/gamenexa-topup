@extends('layouts.admin')

@section('title', 'Dashboard Administrator — GameNexa')
@section('header-title', 'Overview & Statistik')
@section('header-subtitle', 'Ringkasan performa penjualan, katalog produk, dan aktivitas transaksi')

@section('content')
<div class="space-y-8">
    <!-- Quick Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Revenue Card -->
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden glass-card-hover transition-all">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl"></div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Total Omset (Revenue)</span>
                <span class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-sm border border-purple-500/20">
                    <i class="fa-solid fa-rupiah-sign"></i>
                </span>
            </div>
            <div class="font-heading font-black text-2xl text-white mt-3">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
            <div class="text-xs text-emerald-400 mt-2 flex items-center gap-1.5 font-semibold">
                <i class="fa-solid fa-circle-check text-[9px]"></i>
                <span>Transaksi Sukses Terverifikasi</span>
            </div>
        </div>

        <!-- Profit Card -->
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden glass-card-hover transition-all">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl"></div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Estimasi Profit Bersih</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm border border-emerald-500/20">
                    <i class="fa-solid fa-chart-line"></i>
                </span>
            </div>
            <div class="font-heading font-black text-2xl text-emerald-400 mt-3">
                Rp {{ number_format($estimatedProfit, 0, ',', '.') }}
            </div>
            <div class="text-xs text-zinc-400 mt-2 font-mono text-[11px]">
                (Harga Jual - Harga Modal)
            </div>
        </div>

        <!-- Orders Card -->
        <div class="glass-card rounded-2xl p-6 glass-card-hover transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Total Pesanan</span>
                <span class="w-8 h-8 rounded-xl bg-fuchsia-500/10 text-fuchsia-400 flex items-center justify-center text-sm border border-fuchsia-500/20">
                    <i class="fa-solid fa-receipt"></i>
                </span>
            </div>
            <div class="font-heading font-black text-2xl text-white mt-3">
                {{ number_format($totalOrders) }}
            </div>
            <div class="flex items-center gap-3 text-xs mt-2 font-semibold">
                <span class="text-emerald-400">{{ $successOrders }} Sukses</span>
                <span class="text-zinc-600">&bull;</span>
                <span class="text-amber-400">{{ $pendingOrders }} Pending</span>
                <span class="text-zinc-600">&bull;</span>
                <span class="text-red-400">{{ $failedOrders }} Gagal</span>
            </div>
        </div>

        <!-- Catalog Card -->
        <div class="glass-card rounded-2xl p-6 glass-card-hover transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Katalog Produk</span>
                <span class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-sm border border-cyan-500/20">
                    <i class="fa-solid fa-gamepad"></i>
                </span>
            </div>
            <div class="font-heading font-black text-2xl text-white mt-3">
                {{ number_format($activeProducts) }} <span class="text-sm font-semibold text-zinc-400">/ {{ $totalProducts }}</span>
            </div>
            <div class="text-xs text-purple-300 mt-2 font-semibold">
                Tersebar di {{ $totalCategories }} Kategori Game
            </div>
        </div>
    </div>

    <!-- Quick Actions Banner -->
    <div class="glass-card rounded-2xl p-6 border-purple-500/30 bg-gradient-to-r from-purple-950/40 via-dark-800 to-dark-800 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="font-heading font-bold text-base text-white">Menu Cepat Pengelolaan</h3>
            <p class="text-xs text-zinc-400 mt-0.5">Akses fitur utama manajemen katalog dan sinkronisasi produk dalam 1 klik</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.categories.create') }}" class="px-3.5 py-2 rounded-xl bg-dark-700 hover:bg-dark-600 text-zinc-300 hover:text-white text-xs font-bold border border-purple-900/40 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-folder-plus text-purple-400"></i>
                <span>Tambah Kategori</span>
            </a>
            <a href="{{ route('admin.products.create') }}" class="px-3.5 py-2 rounded-xl bg-dark-700 hover:bg-dark-600 text-zinc-300 hover:text-white text-xs font-bold border border-purple-900/40 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-plus text-purple-400"></i>
                <span>Tambah Produk</span>
            </a>
            <a href="{{ route('admin.digiflazz.index') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:from-purple-500 hover:to-fuchsia-500 text-white text-xs font-bold shadow-lg shadow-purple-600/30 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-arrows-rotate"></i>
                <span>Sinkronisasi Digiflazz</span>
            </a>
        </div>
    </div>

    <!-- Main Content Grid (Recent Orders & Category Distribution) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Orders Table -->
        <div class="lg:col-span-2 glass-card rounded-2xl p-6 shadow-xl shadow-black/20">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-purple-900/30">
                <h3 class="font-heading font-extrabold text-base text-white flex items-center gap-2.5">
                    <i class="fa-solid fa-clock-rotate-left text-purple-400"></i>
                    <span>Pesanan Terbaru</span>
                </h3>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-purple-400 hover:text-purple-300 flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-purple-900/20 text-[10px] font-bold uppercase tracking-wider text-zinc-400">
                            <th class="py-2.5 px-3">Invoice</th>
                            <th class="py-2.5 px-3">Produk</th>
                            <th class="py-2.5 px-3 text-right">Total</th>
                            <th class="py-2.5 px-3 text-center">Status</th>
                            <th class="py-2.5 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-purple-900/10 text-xs text-zinc-300">
                        @forelse($recentOrders as $ord)
                            @php
                                $badgeClass = match($ord->status) {
                                    'completed' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',
                                    'paid', 'processing' => 'bg-purple-500/15 text-purple-300 border-purple-500/30',
                                    'unpaid' => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                                    default => 'bg-red-500/15 text-red-400 border-red-500/30',
                                };
                                $item = $ord->items->first();
                            @endphp
                            <tr class="hover:bg-purple-950/20 transition-colors">
                                <td class="py-3 px-3">
                                    <span class="font-mono font-bold text-white text-xs">{{ $ord->order_number }}</span>
                                    <div class="text-[10px] text-zinc-500">{{ $ord->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="font-bold text-white text-xs">{{ $item->product->category->name ?? 'Game' }}</div>
                                    <div class="text-[11px] text-purple-300">{{ $item->product->name ?? 'Item' }}</div>
                                </td>
                                <td class="py-3 px-3 text-right font-mono font-bold text-white">
                                    Rp {{ number_format($ord->total, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider border {{ $badgeClass }}">
                                        {{ $ord->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <a href="{{ route('admin.orders.show', $ord) }}" class="p-1.5 rounded-lg bg-dark-800 hover:bg-purple-600/30 text-purple-400 hover:text-white transition-colors">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-zinc-500 italic">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Category & Product Distribution -->
        <div class="glass-card rounded-2xl p-6 shadow-xl shadow-black/20 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-purple-900/30">
                    <h3 class="font-heading font-extrabold text-base text-white flex items-center gap-2.5">
                        <i class="fa-solid fa-layer-group text-purple-400"></i>
                        <span>Distribusi Produk</span>
                    </h3>
                    <a href="{{ route('admin.categories.index') }}" class="text-xs font-bold text-purple-400 hover:text-purple-300">Semua</a>
                </div>

                <div class="space-y-3.5">
                    @forelse($categoriesSummary as $cSum)
                        <div class="p-3 rounded-xl bg-dark-900/70 border border-purple-900/30 flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-xs text-white">{{ $cSum->name }}</h4>
                                <span class="text-[10px] text-zinc-500 uppercase tracking-wider">{{ $cSum->type }} &bull; {{ $cSum->publisher ?? 'GameNexa' }}</span>
                            </div>
                            <a href="{{ route('admin.products.index', ['category_id' => $cSum->id]) }}" class="px-2.5 py-1 rounded-full bg-dark-800 hover:bg-purple-950 text-[11px] font-bold text-purple-300 border border-purple-800/40 flex items-center gap-1.5 transition-colors">
                                <span>{{ $cSum->active_products_count }}</span>
                                <span class="text-zinc-500 text-[9px]">/ {{ $cSum->products_count }}</span>
                                <i class="fa-solid fa-arrow-right text-[9px]"></i>
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-zinc-500 italic">Belum ada kategori.</p>
                    @endforelse
                </div>
            </div>

            <div class="pt-6 border-t border-purple-900/20 text-center">
                <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-purple-400 hover:text-purple-300 inline-flex items-center gap-1.5">
                    <span>Buka Seluruh Katalog Produk ({{ $totalProducts }})</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
