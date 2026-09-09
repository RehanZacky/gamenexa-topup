@extends('layouts.admin')

@section('title', 'Katalog Produk & Harga — Admin GameNexa')
@section('header-title', 'Katalog Produk & Harga Jual')
@section('header-subtitle', 'Kelola SKU Digiflazz, harga modal, harga jual customer, dan margin profit')

@section('content')
<div class="space-y-6">
    <!-- Top Action & Filter Bar -->
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
        <!-- Search & Filter Form -->
        <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[220px]">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk / SKU (e.g. ML86)..."
                       class="w-full bg-dark-800/90 border border-purple-900/30 rounded-xl pl-9 pr-4 py-2.5 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/30">
            </div>

            <select name="category_id" onchange="this.form.submit()" class="bg-dark-800/90 border border-purple-900/30 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-purple-500 max-w-[200px]">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="bg-dark-800/90 border border-purple-900/30 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-purple-500">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            @if(request()->hasAny(['search', 'category_id', 'status']))
                <a href="{{ route('admin.products.index') }}" class="px-3 py-2.5 rounded-xl bg-dark-700 text-zinc-400 hover:text-white text-xs font-semibold flex items-center gap-1.5 border border-purple-900/30">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset</span>
                </a>
            @endif
        </form>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2.5">
            <!-- Bulk Price Button -->
            <button type="button" onclick="openBulkModal()" class="px-4 py-2.5 rounded-xl bg-purple-950/60 hover:bg-purple-900/60 text-purple-300 font-bold text-xs border border-purple-500/30 transition-all flex items-center gap-2">
                <i class="fa-solid fa-sliders"></i>
                <span>Bulk Update Harga</span>
            </button>

            <!-- Create Button -->
            <a href="{{ route('admin.products.create') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:from-purple-500 hover:to-fuchsia-500 text-white font-bold text-xs shadow-lg shadow-purple-600/30 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Produk</span>
            </a>
        </div>
    </div>

    <!-- Product Table Card -->
    <div class="glass-card rounded-2xl overflow-hidden shadow-xl shadow-black/20">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-purple-900/30 bg-dark-800/80 text-[11px] font-extrabold uppercase tracking-wider text-zinc-400">
                        <th class="py-4 px-4 w-12 text-center">
                            <input type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAll(this)" class="w-4 h-4 rounded bg-dark-900 border-purple-900/50 text-purple-600 focus:ring-0">
                        </th>
                        <th class="py-4 px-5">Nama Produk</th>
                        <th class="py-4 px-5">Kategori</th>
                        <th class="py-4 px-5">SKU / Provider</th>
                        <th class="py-4 px-5 text-right">Harga Modal</th>
                        <th class="py-4 px-5 text-right">Harga Jual</th>
                        <th class="py-4 px-5 text-right">Margin / Profit</th>
                        <th class="py-4 px-5 text-center">Status</th>
                        <th class="py-4 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-900/15 text-xs text-zinc-300">
                    @forelse($products as $prod)
                        @php
                            $margin = $prod->selling_price - $prod->modal_price;
                            $marginPercent = $prod->modal_price > 0 ? round(($margin / $prod->modal_price) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-purple-950/20 transition-colors">
                            <td class="py-4 px-4 text-center">
                                <input type="checkbox" name="selected_ids[]" value="{{ $prod->id }}" class="prod-checkbox w-4 h-4 rounded bg-dark-900 border-purple-900/50 text-purple-600 focus:ring-0">
                            </td>
                            <td class="py-4 px-5">
                                <div class="font-bold text-white text-sm">{{ $prod->name }}</div>
                                @if($prod->sort_order > 0)
                                    <span class="text-[10px] text-zinc-500 font-mono">Urutan: #{{ $prod->sort_order }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-5">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-500/10 text-purple-300 border border-purple-500/20">
                                    {{ $prod->category->name ?? '—' }}
                                </span>
                            </td>
                            <td class="py-4 px-5">
                                <code class="font-mono text-xs text-purple-300 bg-purple-950/40 px-2 py-0.5 rounded border border-purple-800/40">{{ $prod->provider_product_code }}</code>
                                <div class="text-[10px] text-zinc-500 mt-0.5">{{ $prod->provider->name ?? 'Digiflazz' }}</div>
                            </td>
                            <td class="py-4 px-5 text-right font-mono text-zinc-400">
                                Rp {{ number_format($prod->modal_price, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5 text-right font-mono font-bold text-white text-sm">
                                Rp {{ number_format($prod->selling_price, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5 text-right font-mono">
                                <div class="font-bold {{ $margin >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                    Rp {{ number_format($margin, 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] {{ $margin >= 0 ? 'text-emerald-400/70' : 'text-red-400/70' }}">
                                    {{ $margin >= 0 ? '+' : '' }}{{ $marginPercent }}%
                                </div>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <form action="{{ route('admin.products.toggle-status', $prod) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" title="Klik untuk mengubah status"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider transition-all {{ $prod->status === 'active' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/25' : 'bg-zinc-700/40 text-zinc-400 border border-zinc-600/30 hover:bg-zinc-700/60' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $prod->status === 'active' ? 'bg-emerald-400' : 'bg-zinc-500' }}"></span>
                                        <span>{{ $prod->status }}</span>
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $prod) }}" class="p-2 rounded-lg bg-dark-800 hover:bg-purple-600/30 text-purple-400 hover:text-white border border-purple-900/30 transition-colors" title="Edit Produk">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form action="{{ route('admin.products.destroy', $prod) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ $prod->name }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-dark-800 hover:bg-red-500/30 text-red-400 hover:text-white border border-purple-900/30 transition-colors" title="Hapus Produk">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-zinc-500">
                                <i class="fa-solid fa-box-open text-3xl mb-3 block text-zinc-600"></i>
                                <span class="font-semibold text-sm">Tidak ada data produk ditemukan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-4 border-t border-purple-900/20 bg-dark-800/50">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Bulk Price Update Modal -->
<div id="bulkPriceModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="glass-card rounded-2xl w-full max-w-lg p-6 sm:p-8 shadow-2xl border border-purple-500/30">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-purple-900/30">
            <div>
                <h3 class="font-heading font-extrabold text-lg text-white">Bulk Update Harga Jual</h3>
                <p class="text-xs text-zinc-400 mt-0.5">Perbarui harga jual beberapa produk sekaligus</p>
            </div>
            <button onclick="closeBulkModal()" class="text-zinc-400 hover:text-white p-1.5"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <form action="{{ route('admin.products.bulk-price-update') }}" method="POST" id="bulkForm" class="space-y-5">
            @csrf
            <div id="selectedProductsInputContainer"></div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Target Pembaruan</label>
                <div class="p-3 rounded-xl bg-dark-900/80 border border-purple-900/40 text-xs">
                    <span id="selectedCountText" class="font-bold text-purple-300">0 produk dipilih pada tabel</span>
                    <span class="block text-[11px] text-zinc-500 mt-1">Atau pilih kategori target di bawah jika tidak mencentang baris produk:</span>
                </div>
            </div>

            <div>
                <label for="target_category_id" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Target Seluruh Kategori (Opsional)</label>
                <select name="target_category_id" id="target_category_id" class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-purple-500">
                    <option value="">— Hanya Produk yang Dicentang —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }} (Semua Produk)</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="adjustment_type" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Metode Penyesuaian Harga</label>
                <select name="adjustment_type" id="adjustment_type" required class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-purple-500">
                    <option value="set_margin">Set Harga = Modal + Margin Rp (Rekomendasi)</option>
                    <option value="add_nominal">Tambah Nominal Rp (Harga Jual + Rp X)</option>
                    <option value="add_percent">Tambah Persentase % (Harga Jual + X%)</option>
                    <option value="set_exact">Set Harga Jual Tetap Rp</option>
                </select>
            </div>

            <div>
                <label for="adjustment_value" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Nilai Penyesuaian</label>
                <div class="relative">
                    <input type="number" name="adjustment_value" id="adjustment_value" value="2000" required step="1"
                           class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl px-4 py-2.5 text-sm text-white font-mono focus:outline-none focus:border-purple-500" placeholder="2000">
                </div>
                <small class="block text-[11px] text-zinc-400 mt-1">Contoh: masukkan <code>2000</code> untuk margin Rp2.000 atau <code>10</code> untuk 10%.</small>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-purple-900/30">
                <button type="button" onclick="closeBulkModal()" class="px-4 py-2.5 rounded-xl bg-dark-800 hover:bg-dark-700 text-zinc-300 font-bold text-xs">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:from-purple-500 hover:to-fuchsia-500 text-white font-bold text-xs shadow-lg shadow-purple-600/30 flex items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Terapkan Harga</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleSelectAll(master) {
        const checkboxes = document.querySelectorAll('.prod-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
    }

    function openBulkModal() {
        const checked = document.querySelectorAll('.prod-checkbox:checked');
        const container = document.getElementById('selectedProductsInputContainer');
        const text = document.getElementById('selectedCountText');
        container.innerHTML = '';

        if (checked.length > 0) {
            text.innerText = `${checked.length} produk dipilih pada tabel`;
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'product_ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });
        } else {
            text.innerText = '0 produk dipilih (Akan menggunakan Target Kategori)';
        }

        const modal = document.getElementById('bulkPriceModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeBulkModal() {
        const modal = document.getElementById('bulkPriceModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>
@endsection
