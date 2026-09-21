@extends('layouts.admin')

@section('title', 'Edit Produk ' . $product->name . ' — Admin GameNexa')
@section('header-title', 'Edit Produk: ' . $product->name)
@section('header-subtitle', 'Perbarui harga jual customer, modal, nama, dan status produk')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass-card rounded-2xl p-6 sm:p-8 shadow-xl shadow-black/20">
        <div class="flex items-center justify-between pb-6 mb-6 border-b border-purple-900/30">
            <h2 class="font-heading font-bold text-lg text-white">Edit Informasi Produk</h2>
            <a href="{{ route('admin.products.index', ['category_id' => $product->category_id]) }}" class="text-xs text-zinc-400 hover:text-white flex items-center gap-1.5 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Daftar</span>
            </a>
        </div>

        @if($hasOrderHistory)
            <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs font-semibold flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-base text-amber-400"></i>
                <span>Perhatian: Produk ini sudah memiliki histori transaksi order. Hindari mengubah <strong>Provider Product Code (SKU)</strong> kecuali terjadi perubahan kode resmi dari Digiflazz.</span>
            </div>
        @endif

        <form action="{{ route('admin.products.update', $product) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">
                        Kategori Game / Produk <span class="text-purple-400">*</span>
                    </label>
                    <select name="category_id" id="category_id" required onchange="onCategoryChanged()"
                            class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-purple-500 transition-colors">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" data-slug="{{ $cat->slug }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Provider -->
                <div>
                    <label for="provider_id" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Provider H2H <span class="text-purple-400">*</span></label>
                    <select name="provider_id" id="provider_id" required class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-purple-500">
                        @foreach($providers as $prov)
                            <option value="{{ $prov->id }}" {{ old('provider_id', $product->provider_id) == $prov->id ? 'selected' : '' }}>{{ $prov->name }}</option>
                        @endforeach
                    </select>
                    @error('provider_id') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Provider SKU Dropdown / Input -->
                <div class="md:col-span-2 p-4 rounded-xl bg-purple-950/20 border border-purple-900/40 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <label for="sku_select" class="block text-xs font-bold uppercase tracking-wider text-zinc-200">
                            Pilih SKU / Kode Produk Digiflazz <span class="text-purple-400">*</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <span id="skuLoading" class="hidden text-xs text-purple-400 font-semibold items-center gap-1.5 animate-pulse">
                                <i class="fa-solid fa-spinner fa-spin"></i> Sinkronisasi Digiflazz...
                            </span>
                            <button type="button" onclick="refreshDigiflazzSkus()" id="refreshBtn" title="Segarkan data Digiflazz"
                                    class="text-xs text-purple-300 hover:text-purple-100 flex items-center gap-1 py-1 px-2.5 rounded-lg bg-purple-900/40 hover:bg-purple-900/70 border border-purple-700/40 transition-all">
                                <i class="fa-solid fa-rotate text-[10px]"></i>
                                <span>Sync Ulang Digiflazz</span>
                            </button>
                            <button type="button" onclick="toggleManualSku()" id="toggleManualBtn"
                                    class="text-xs text-zinc-400 hover:text-zinc-200 underline">
                                Input Manual
                            </button>
                        </div>
                    </div>

                    <!-- Dropdown Mode (Default) -->
                    <div id="skuDropdownContainer">
                        <select id="sku_select" onchange="onSkuSelected(this)"
                                class="w-full bg-dark-900/90 border border-purple-500/40 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30 font-mono">
                            <option value="{{ $product->provider_product_code }}">{{ $product->provider_product_code }} — {{ $product->name }} (Saat ini)</option>
                        </select>
                        <div id="skuStatusNotice" class="hidden mt-2 p-2.5 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span id="skuStatusText"></span>
                        </div>
                    </div>

                    <!-- Manual Input Mode (Hidden by default) -->
                    <div id="skuManualContainer" class="hidden space-y-2">
                        <input type="text" name="provider_product_code" id="provider_product_code" value="{{ old('provider_product_code', $product->provider_product_code) }}" required
                               class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl px-4 py-3 text-sm text-white font-mono focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/30">
                        <p class="text-[11px] text-zinc-400">Mode manual aktif. Masukkan SKU Digiflazz persis sesuai buyer_sku_code di Digiflazz.</p>
                    </div>
                    @error('provider_product_code') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Product Name -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-zinc-300">
                            Nama Nominal Produk <span class="text-purple-400">*</span>
                        </label>
                        <span id="nominalBadge" class="hidden inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                            <i class="fa-solid fa-bolt text-[9px] text-purple-400"></i> Auto-Sync: <span id="nominalValueText"></span>
                        </span>
                    </div>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                           class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/30 transition-all">
                    <small class="block text-[11px] text-zinc-400 mt-1">Otomatis terisi nominal produk saat memilih SKU Digiflazz.</small>
                    @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Modal Price (Locked / Paten dari Digiflazz) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="modal_price" class="block text-xs font-bold uppercase tracking-wider text-zinc-300">
                            Harga Modal (Digiflazz)
                        </label>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                            <i class="fa-solid fa-lock text-[9px]"></i> Paten / Terkunci
                        </span>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-xs font-bold text-zinc-500">Rp</span>
                        <input type="number" name="modal_price" id="modal_price" value="{{ (int)$product->modal_price }}" readonly
                               class="w-full bg-dark-950/70 border border-purple-900/20 rounded-xl pl-12 pr-10 py-3 text-sm text-zinc-300 font-mono font-bold cursor-not-allowed select-none focus:outline-none">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-zinc-500">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </span>
                    </div>
                    <small class="block text-[11px] text-zinc-400 mt-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info text-amber-400"></i>
                        Harga modal otomatis diperbarui melalui menu <a href="{{ route('admin.digiflazz.index') }}" class="text-purple-400 hover:underline font-semibold">Sinkronisasi Digiflazz</a>.
                    </small>
                </div>

                <!-- Selling Price -->
                <div class="md:col-span-2">
                    <label for="selling_price" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">
                        Harga Jual Customer <span class="text-purple-400">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-purple-400">Rp</span>
                        <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', (int)$product->selling_price) }}" required min="0" step="1" oninput="calcMargin()"
                               class="w-full bg-dark-900/90 border border-purple-500/50 rounded-xl pl-12 pr-4 py-3.5 text-base text-white font-mono font-bold focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/30 transition-all">
                    </div>
                    <small class="block text-[11px] text-zinc-400 mt-1">Masukkan nominal harga jual yang akan dibayar oleh customer.</small>
                    @error('selling_price') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Live Margin Box -->
            <div class="p-4 rounded-xl bg-purple-950/30 border border-purple-500/20 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="text-xs text-zinc-400 font-semibold uppercase tracking-wider">Estimasi Margin Keuntungan Bersih:</span>
                    <div class="font-heading font-black text-xl text-emerald-400 mt-0.5" id="marginDisplay">Rp 0 (0%)</div>
                </div>
                <div class="text-xs text-zinc-400">
                    Formula: <code class="text-purple-300">Harga Jual - Harga Modal (Digiflazz)</code>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sort Order -->
                <div>
                    <label for="sort_order" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Urutan Tampilan (Sort Order)</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $product->sort_order) }}" min="0"
                           class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl px-4 py-3 text-sm text-white font-mono focus:outline-none focus:border-purple-500">
                    <small class="block text-[11px] text-zinc-400 mt-1">Nilai lebih kecil akan tampil lebih awal di halaman checkout.</small>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Status Produk</label>
                    <div class="flex items-center gap-6 mt-3">
                        <label class="flex items-center gap-2 cursor-pointer text-sm">
                            <input type="radio" name="status" value="active" {{ old('status', $product->status) === 'active' ? 'checked' : '' }} class="text-purple-600 focus:ring-purple-500">
                            <span class="text-white font-semibold">Active</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm">
                            <input type="radio" name="status" value="inactive" {{ old('status', $product->status) === 'inactive' ? 'checked' : '' }} class="text-purple-600 focus:ring-purple-500">
                            <span class="text-zinc-400 font-semibold">Inactive</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Keterangan / Deskripsi Produk (Opsional)</label>
                <textarea name="description" id="description" rows="2"
                          class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-purple-500">{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-purple-900/30">
                <a href="{{ route('admin.products.index', ['category_id' => $product->category_id]) }}" class="px-5 py-2.5 rounded-xl bg-dark-800 hover:bg-dark-700 text-zinc-300 font-bold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:from-purple-500 hover:to-fuchsia-500 text-white font-bold text-xs shadow-lg shadow-purple-600/30 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let digiflazzProducts = [];
    let isManualMode = false;
    const currentSku = "{{ $product->provider_product_code }}";

    function onCategoryChanged() {
        const categoryId = document.getElementById('category_id').value;
        if (!categoryId) {
            const select = document.getElementById('sku_select');
            select.innerHTML = '<option value="">— Silakan Pilih Kategori Game Terlebih Dahulu —</option>';
            return;
        }

        fetchDigiflazzSkus(categoryId, false);
    }

    function refreshDigiflazzSkus() {
        const categoryId = document.getElementById('category_id').value;
        if (!categoryId) {
            alert('Silakan pilih kategori game terlebih dahulu.');
            return;
        }
        fetchDigiflazzSkus(categoryId, true);
    }

    async function fetchDigiflazzSkus(categoryId, refresh = false) {
        const loadingEl = document.getElementById('skuLoading');
        const select = document.getElementById('sku_select');
        const noticeEl = document.getElementById('skuStatusNotice');
        
        loadingEl.classList.remove('hidden');
        loadingEl.classList.add('inline-flex');
        select.disabled = true;
        select.innerHTML = '<option value="">⏳ Mengambil daftar SKU dari Digiflazz...</option>';
        noticeEl.classList.add('hidden');

        try {
            const url = `{{ route('admin.products.digiflazz-skus') }}?category_id=${categoryId}${refresh ? '&refresh=1' : ''}`;
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();

            if (data.success && data.products && data.products.length > 0) {
                digiflazzProducts = data.products;
                select.innerHTML = `<option value="">— Pilih SKU Digiflazz (${data.count} produk tersedia) —</option>`;
                
                let foundCurrent = false;
                data.products.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.sku;
                    opt.dataset.name = p.clean_name;
                    opt.dataset.rawname = p.name;
                    opt.dataset.price = p.price;
                    opt.dataset.desc = p.desc || '';
                    opt.dataset.active = p.is_active ? '1' : '0';
                    
                    const priceFormatted = new Intl.NumberFormat('id-ID').format(p.price);
                    const statusText = p.is_active ? '' : ' [GANGGUAN/OFF]';
                    opt.textContent = `${p.sku} — ${p.clean_name} (Modal: Rp ${priceFormatted})${statusText}`;
                    
                    if (p.sku === currentSku) {
                        opt.selected = true;
                        foundCurrent = true;
                    }

                    select.appendChild(opt);
                });

                // Jika current SKU tidak ada dalam daftar digiflazz aktif
                if (!foundCurrent && currentSku) {
                    const customOpt = document.createElement('option');
                    customOpt.value = currentSku;
                    customOpt.textContent = `${currentSku} (SKU Saat Ini - Tidak ditemukan di response Digiflazz)`;
                    customOpt.selected = true;
                    select.prepend(customOpt);
                }

                select.disabled = false;
            } else {
                const msg = data.message || 'Tidak ada SKU Digiflazz yang cocok dengan kategori ini.';
                select.innerHTML = `<option value="${currentSku}">${currentSku} — ${msg}</option>`;
                select.disabled = false;
                noticeEl.classList.remove('hidden');
                document.getElementById('skuStatusText').textContent = msg + ' Anda dapat beralih ke Input Manual.';
            }
        } catch (err) {
            console.error('Error fetching Digiflazz SKUs:', err);
            select.innerHTML = `<option value="${currentSku}">${currentSku} — Gagal memuat dari Digiflazz</option>`;
            select.disabled = false;
            noticeEl.classList.remove('hidden');
            document.getElementById('skuStatusText').textContent = 'Koneksi ke Digiflazz bermasalah. Anda dapat menggunakan opsi Input Manual.';
        } finally {
            loadingEl.classList.add('hidden');
            loadingEl.classList.remove('inline-flex');
        }
    }

    function onSkuSelected(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        if (!selectedOption || !selectedOption.value) return;

        const sku = selectedOption.value;
        const name = selectedOption.dataset.name || selectedOption.dataset.rawname || '';
        const price = parseFloat(selectedOption.dataset.price) || 0;
        const desc = selectedOption.dataset.desc || '';
        const isActive = selectedOption.dataset.active === '1';

        // Update Hidden/Manual input SKU
        document.getElementById('provider_product_code').value = sku;

        // Jika opsi memiliki data name & price dari Digiflazz
        if (name) {
            const nameInput = document.getElementById('name');
            nameInput.value = name;
            nameInput.classList.add('ring-2', 'ring-purple-500/50');
            setTimeout(() => nameInput.classList.remove('ring-2', 'ring-purple-500/50'), 600);
        }

        if (price > 0) {
            const modalInput = document.getElementById('modal_price');
            modalInput.value = price;
        }

        // Tampilkan status peringatan jika produk dari Digiflazz gangguan
        const noticeEl = document.getElementById('skuStatusNotice');
        if (!isActive && selectedOption.dataset.active !== undefined) {
            noticeEl.classList.remove('hidden');
            document.getElementById('skuStatusText').textContent = `SKU ${sku} saat ini berstatus Cut-Off / Gangguan di server Digiflazz.`;
        } else {
            noticeEl.classList.add('hidden');
        }

        // Recalculate Margin
        calcMargin();
    }

    function toggleManualSku() {
        isManualMode = !isManualMode;
        const dropdownContainer = document.getElementById('skuDropdownContainer');
        const manualContainer = document.getElementById('skuManualContainer');
        const toggleBtn = document.getElementById('toggleManualBtn');

        if (isManualMode) {
            dropdownContainer.classList.add('hidden');
            manualContainer.classList.remove('hidden');
            toggleBtn.textContent = 'Kembali ke Dropdown';
        } else {
            dropdownContainer.classList.remove('hidden');
            manualContainer.classList.add('hidden');
            toggleBtn.textContent = 'Input Manual';
        }
    }

    function calcMargin() {
        const modal = parseFloat(document.getElementById('modal_price').value) || 0;
        const sell = parseFloat(document.getElementById('selling_price').value) || 0;
        const margin = sell - modal;
        const percent = modal > 0 ? ((margin / modal) * 100).toFixed(1) : 0;
        const display = document.getElementById('marginDisplay');

        if (margin >= 0) {
            display.className = 'font-heading font-black text-xl text-emerald-400 mt-0.5';
            display.innerText = `+Rp ${margin.toLocaleString('id-ID')} (+${percent}%)`;
        } else {
            display.className = 'font-heading font-black text-xl text-red-400 mt-0.5';
            display.innerText = `-Rp ${Math.abs(margin).toLocaleString('id-ID')} (${percent}%)`;
        }
    }

    // Auto-load on page load
    document.addEventListener('DOMContentLoaded', () => {
        const catSelect = document.getElementById('category_id');
        if (catSelect.value) {
            fetchDigiflazzSkus(catSelect.value, false);
        }
        calcMargin();
    });
</script>
@endsection
