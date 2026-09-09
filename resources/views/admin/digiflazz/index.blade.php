@extends('layouts.admin')

@section('title', 'Integrasi & Sinkronisasi Digiflazz — Admin GameNexa')
@section('header-title', 'Integrasi Digiflazz H2H')
@section('header-subtitle', 'Pusat kontrol koneksi API, cek saldo deposit, dan sinkronisasi katalog produk')

@section('content')
<div class="space-y-8 max-w-6xl">
    <!-- Top Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Balance Card -->
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl"></div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Saldo Deposit</span>
                <span class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-sm border border-purple-500/20">
                    <i class="fa-solid fa-wallet"></i>
                </span>
            </div>
            <div class="font-heading font-black text-2xl text-white mt-3">
                Rp {{ number_format((float)$balance, 0, ',', '.') }}
            </div>
            <div class="flex items-center gap-2 mt-2 text-xs font-semibold {{ $balanceRc === '00' || is_numeric($balance) ? 'text-emerald-400' : 'text-amber-400' }}">
                <i class="fa-solid fa-circle text-[8px]"></i>
                <span>{{ $balanceMsg }}</span>
            </div>
        </div>

        <!-- Total Products -->
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Produk Digiflazz</span>
                <span class="w-8 h-8 rounded-xl bg-fuchsia-500/10 text-fuchsia-400 flex items-center justify-center text-sm border border-fuchsia-500/20">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </span>
            </div>
            <div class="font-heading font-black text-2xl text-white mt-3">
                {{ number_format($totalProducts) }}
            </div>
            <div class="text-xs text-zinc-400 mt-2">
                Tersimpan di database
            </div>
        </div>

        <!-- Active Products -->
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Produk Aktif</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm border border-emerald-500/20">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
            </div>
            <div class="font-heading font-black text-2xl text-emerald-400 mt-3">
                {{ number_format($activeProducts) }}
            </div>
            <div class="text-xs text-zinc-400 mt-2">
                Aktif di halaman customer
            </div>
        </div>

        <!-- Last Sync -->
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Terakhir Sinkron</span>
                <span class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-sm border border-cyan-500/20">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </span>
            </div>
            <div class="font-heading font-extrabold text-base text-white mt-3">
                {{ $lastSync ? $lastSync->diffForHumans() : 'Belum pernah' }}
            </div>
            <div class="text-xs text-zinc-500 mt-2 font-mono text-[11px]">
                {{ $lastSync ? $lastSync->format('d M Y, H:i') . ' WIB' : '—' }}
            </div>
        </div>
    </div>

    <!-- Sync Action Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sync Trigger Card -->
        <div class="lg:col-span-2 glass-card rounded-2xl p-6 sm:p-8 shadow-xl shadow-black/20">
            <div class="flex items-center gap-3 pb-5 mb-6 border-b border-purple-900/30">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-purple-600 to-fuchsia-600 flex items-center justify-center text-white text-lg shadow-md shadow-purple-600/30">
                    <i class="fa-solid fa-cloud-arrow-down"></i>
                </div>
                <div>
                    <h3 class="font-heading font-black text-lg text-white">Sinkronisasi Katalog & Harga Produk</h3>
                    <p class="text-xs text-zinc-400 mt-0.5">Tarik daftar harga modal dan SKU terbaru secara langsung dari server Digiflazz</p>
                </div>
            </div>

            <form action="{{ route('admin.digiflazz.sync') }}" method="POST" class="space-y-6" onsubmit="return confirm('Mulai proses sinkronisasi produk dari Digiflazz? Proses ini dapat memakan waktu beberapa detik.')">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Category Filter -->
                    <div>
                        <label for="category_filter" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Filter Brand / Kategori Tertentu (Opsional)</label>
                        <select name="category_filter" id="category_filter" class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-purple-500">
                            <option value="">— Tarik Semua Produk & Game —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <small class="block text-[11px] text-zinc-500 mt-1">Kosongkan untuk menarik seluruh katalog (Games, Pulsa, PLN, dll).</small>
                    </div>

                    <!-- Default Margin -->
                    <div>
                        <label for="default_margin" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Margin Keuntungan Default (Produk Baru) <span class="text-purple-400">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-xs font-bold text-zinc-400">Rp</span>
                            <input type="number" name="default_margin" id="default_margin" value="2000" min="0" step="100" required
                                   class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl pl-12 pr-4 py-3 text-sm text-white font-mono focus:outline-none focus:border-purple-500" placeholder="2000">
                        </div>
                        <small class="block text-[11px] text-zinc-500 mt-1">Diterapkan otomatis untuk produk yang baru pertama kali masuk.</small>
                    </div>
                </div>

                <!-- Preserve Selling Price Checkbox -->
                <div class="p-4 rounded-xl bg-dark-900/70 border border-purple-900/30 flex items-start gap-3">
                    <input type="checkbox" name="update_selling_price" id="update_selling_price" value="1"
                           class="mt-1 w-4 h-4 rounded bg-dark-800 border-purple-900/50 text-purple-600 focus:ring-0">
                    <label for="update_selling_price" class="text-xs cursor-pointer select-none">
                        <span class="font-bold text-white block">Perbarui juga Harga Jual pada Produk yang Sudah Ada (Recalculate Selling Price)</span>
                        <span class="text-zinc-400 block mt-0.5">Jika TIDAK dicentang, sistem hanya memperbarui harga modal (provider_price) dan <strong>mempertahankan harga jual khusus</strong> yang telah Anda atur sebelumnya.</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                            class="w-full py-4 px-6 rounded-xl bg-gradient-to-r from-purple-600 via-purple-500 to-fuchsia-600 hover:from-purple-500 hover:to-fuchsia-500 text-white font-heading font-extrabold text-sm tracking-wider uppercase transition-all duration-200 shadow-xl shadow-purple-600/30 flex items-center justify-center gap-3">
                        <i class="fa-solid fa-arrows-rotate text-base"></i>
                        <span>Mulai Sinkronisasi Produk Digiflazz</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Connection Info / Guide Card -->
        <div class="glass-card rounded-2xl p-6 sm:p-8 flex flex-col justify-between">
            <div>
                <h4 class="font-heading font-bold text-base text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-purple-400"></i>
                    <span>Informasi Koneksi API</span>
                </h4>

                <div class="space-y-3.5 text-xs text-zinc-300 leading-relaxed">
                    <div class="p-3 rounded-xl bg-dark-900/80 border border-purple-900/30">
                        <span class="text-zinc-500 block text-[10px] uppercase font-bold tracking-wider">Username Digiflazz</span>
                        <span class="font-mono text-purple-300 font-bold">{{ config('digiflazz.username') ?: 'Belum disetel di .env' }}</span>
                    </div>

                    <div class="p-3 rounded-xl bg-dark-900/80 border border-purple-900/30">
                        <span class="text-zinc-500 block text-[10px] uppercase font-bold tracking-wider">Mode Environment</span>
                        <span class="font-bold {{ config('digiflazz.mode') === 'production' ? 'text-emerald-400' : 'text-amber-400' }} uppercase">
                            {{ config('digiflazz.mode', 'development') }}
                        </span>
                    </div>

                    <div class="p-3 rounded-xl bg-dark-900/80 border border-purple-900/30">
                        <span class="text-zinc-500 block text-[10px] uppercase font-bold tracking-wider">Webhook Endpoint</span>
                        <span class="font-mono text-[11px] text-zinc-400 break-all">{{ url('/webhook/digiflazz') }}</span>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-purple-900/20 text-xs text-zinc-400">
                <i class="fa-solid fa-shield-halved text-purple-400 mr-1.5"></i>
                Pastikan IP Publik Server Anda sudah di-whitelist di Dashboard Member Digiflazz.
            </div>
        </div>
    </div>
</div>
@endsection
