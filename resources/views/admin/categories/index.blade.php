@extends('layouts.admin')

@section('title', 'Kategori Game & Produk — Admin GameNexa')
@section('header-title', 'Kategori Game & Produk')
@section('header-subtitle', 'Kelola daftar brand, tipe produk, dan konfigurasi field akun')

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <!-- Search & Filter Form -->
        <form action="{{ route('admin.categories.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[220px]">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori / publisher..."
                       class="w-full bg-dark-800/90 border border-purple-900/30 rounded-xl pl-9 pr-4 py-2.5 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/30">
            </div>

            <select name="type" onchange="this.form.submit()" class="bg-dark-800/90 border border-purple-900/30 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-purple-500">
                <option value="">Semua Tipe</option>
                @foreach($types as $key => $label)
                    <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="bg-dark-800/90 border border-purple-900/30 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-purple-500">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            @if(request()->hasAny(['search', 'type', 'status']))
                <a href="{{ route('admin.categories.index') }}" class="px-3 py-2.5 rounded-xl bg-dark-700 text-zinc-400 hover:text-white text-xs font-semibold flex items-center gap-1.5 border border-purple-900/30">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset</span>
                </a>
            @endif
        </form>

        <!-- Create Button -->
        <a href="{{ route('admin.categories.create') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:from-purple-500 hover:to-fuchsia-500 text-white font-bold text-xs shadow-lg shadow-purple-600/30 flex items-center justify-center gap-2 transition-all">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Kategori Baru</span>
        </a>
    </div>

    <!-- Category Table Card -->
    <div class="glass-card rounded-2xl overflow-hidden shadow-xl shadow-black/20">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-purple-900/30 bg-dark-800/80 text-[11px] font-extrabold uppercase tracking-wider text-zinc-400">
                        <th class="py-4 px-5">ID</th>
                        <th class="py-4 px-5">Kategori / Brand</th>
                        <th class="py-4 px-5">Tipe & Publisher</th>
                        <th class="py-4 px-5">Form Target ID</th>
                        <th class="py-4 px-5 text-center">Total Produk</th>
                        <th class="py-4 px-5 text-center">Status</th>
                        <th class="py-4 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-900/15 text-xs text-zinc-300">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-purple-950/20 transition-colors">
                            <td class="py-4 px-5 font-mono text-zinc-500 text-[11px]">#{{ $cat->id }}</td>
                            <td class="py-4 px-5">
                                <div class="font-bold text-white text-sm">{{ $cat->name }}</div>
                                <div class="font-mono text-[11px] text-purple-400 mt-0.5">{{ $cat->slug }}</div>
                            </td>
                            <td class="py-4 px-5">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-500/10 text-purple-300 border border-purple-500/20">
                                    {{ $types[$cat->type] ?? ucfirst($cat->type) }}
                                </span>
                                <div class="text-zinc-400 text-[11px] mt-1">{{ $cat->publisher ?? '—' }}</div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="text-[11px]"><span class="text-zinc-400">ID:</span> <code class="text-purple-300">{{ $cat->user_id_label }}</code></div>
                                @if($cat->has_zone_id)
                                    <div class="text-[11px] mt-0.5"><span class="text-zinc-400">Zone:</span> <code class="text-fuchsia-300">{{ $cat->zone_id_label }}</code></div>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-center">
                                <a href="{{ route('admin.products.index', ['category_id' => $cat->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-dark-800 border border-purple-900/40 hover:border-purple-500 text-xs font-bold text-white hover:text-purple-300 transition-colors">
                                    <span>{{ $cat->active_products_count }}</span>
                                    <span class="text-[10px] text-zinc-500">/ {{ $cat->products_count }}</span>
                                    <i class="fa-solid fa-arrow-right text-[10px] text-purple-400"></i>
                                </a>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <form action="{{ route('admin.categories.toggle-status', $cat) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" title="Klik untuk mengubah status"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider transition-all {{ $cat->status === 'active' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/25' : 'bg-zinc-700/40 text-zinc-400 border border-zinc-600/30 hover:bg-zinc-700/60' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $cat->status === 'active' ? 'bg-emerald-400' : 'bg-zinc-500' }}"></span>
                                        <span>{{ $cat->status }}</span>
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.categories.edit', $cat) }}" class="p-2 rounded-lg bg-dark-800 hover:bg-purple-600/30 text-purple-400 hover:text-white border border-purple-900/30 transition-colors" title="Edit Kategori">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $cat->name }}? Pastikan tidak ada produk terkait.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-dark-800 hover:bg-red-500/30 text-red-400 hover:text-white border border-purple-900/30 transition-colors" title="Hapus Kategori">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-zinc-500">
                                <i class="fa-solid fa-folder-open text-3xl mb-3 block text-zinc-600"></i>
                                <span class="font-semibold text-sm">Tidak ada data kategori ditemukan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="p-4 border-t border-purple-900/20 bg-dark-800/50">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
