@extends('layouts.admin')

@section('title', 'Tambah Kategori Baru — Admin GameNexa')
@section('header-title', 'Tambah Kategori Baru')
@section('header-subtitle', 'Tambahkan game atau kategori produk baru ke katalog GameNexa')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass-card rounded-2xl p-6 sm:p-8 shadow-xl shadow-black/20">
        <div class="flex items-center justify-between pb-6 mb-6 border-b border-purple-900/30">
            <h2 class="font-heading font-bold text-lg text-white">Formulir Kategori Baru</h2>
            <a href="{{ route('admin.categories.index') }}" class="text-xs text-zinc-400 hover:text-white flex items-center gap-1.5 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Daftar</span>
            </a>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Nama Kategori / Game <span class="text-purple-400">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Honor of Kings"
                           class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl px-4 py-3 text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/30">
                    @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Slug URL (Opsional)</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}" placeholder="Contoh: honor-of-kings (otomatis jika kosong)"
                           class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl px-4 py-3 text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/30 font-mono text-xs">
                    @error('slug') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Tipe Kategori <span class="text-purple-400">*</span></label>
                    <select name="type" id="type" required class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-purple-500">
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ old('type', 'games') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Publisher -->
                <div>
                    <label for="publisher" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Publisher / Operator</label>
                    <input type="text" name="publisher" id="publisher" value="{{ old('publisher') }}" placeholder="Contoh: Tencent / Level Infinite"
                           class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl px-4 py-3 text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/30">
                </div>

                <!-- User ID Label -->
                <div>
                    <label for="user_id_label" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Label Input ID Akun</label>
                    <input type="text" name="user_id_label" id="user_id_label" value="{{ old('user_id_label', 'User ID') }}" placeholder="Contoh: User ID / Player ID / No. HP"
                           class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-purple-500">
                </div>

                <!-- Zone ID Label -->
                <div>
                    <label for="zone_id_label" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Label Input Server / Zone</label>
                    <input type="text" name="zone_id_label" id="zone_id_label" value="{{ old('zone_id_label', 'Zone ID') }}" placeholder="Contoh: Zone ID / Server"
                           class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-purple-500">
                </div>
            </div>

            <!-- Has Zone ID Checkbox -->
            <div class="p-4 rounded-xl bg-dark-900/60 border border-purple-900/30 flex items-center justify-between">
                <div>
                    <div class="font-bold text-sm text-white">Memerlukan Zone / Server ID?</div>
                    <div class="text-xs text-zinc-400 mt-0.5">Aktifkan jika game memerlukan form input Zone ID (seperti Mobile Legends / Genshin Impact)</div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="has_zone_id" value="1" {{ old('has_zone_id') ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-zinc-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                </label>
            </div>

            <!-- Instruction -->
            <div>
                <label for="instruction" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Petunjuk Pengisian ID Akun (Untuk Customer)</label>
                <textarea name="instruction" id="instruction" rows="3" placeholder="Contoh: Masukkan Player ID akun game Anda yang tertera di menu profil..."
                          class="w-full bg-dark-900/90 border border-purple-900/40 rounded-xl px-4 py-3 text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-purple-500">{{ old('instruction') }}</textarea>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">Status Publikasi</label>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="radio" name="status" value="active" {{ old('status', 'active') === 'active' ? 'checked' : '' }} class="text-purple-600 focus:ring-purple-500">
                        <span class="text-white font-semibold">Active (Tampil di katalog customer)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="radio" name="status" value="inactive" {{ old('status') === 'inactive' ? 'checked' : '' }} class="text-purple-600 focus:ring-purple-500">
                        <span class="text-zinc-400 font-semibold">Inactive (Disembunyikan)</span>
                    </label>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-purple-900/30">
                <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 rounded-xl bg-dark-800 hover:bg-dark-700 text-zinc-300 font-bold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:from-purple-500 hover:to-fuchsia-500 text-white font-bold text-xs shadow-lg shadow-purple-600/30 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Kategori</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
