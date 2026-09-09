<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Tampilkan Daftar Kategori
     */
    public function index(Request $request): View
    {
        $query = Category::withCount(['products', 'products as active_products_count' => function ($q) {
            $q->where('status', 'active');
        }]);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('publisher', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $categories = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();

        $types = ['games' => 'Game Online', 'pulsa' => 'Pulsa & Data', 'ewallet' => 'E-Wallet', 'pln' => 'Token PLN', 'voucher' => 'Voucher & TV'];

        return view('admin.categories.index', compact('categories', 'types'));
    }

    /**
     * Form Tambah Kategori
     */
    public function create(): View
    {
        $types = ['games' => 'Game Online', 'pulsa' => 'Pulsa & Data', 'ewallet' => 'E-Wallet', 'pln' => 'Token PLN', 'voucher' => 'Voucher & TV'];
        return view('admin.categories.create', compact('types'));
    }

    /**
     * Simpan Kategori Baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:120|unique:categories,slug',
            'type' => 'required|in:games,pulsa,ewallet,pln,voucher',
            'publisher' => 'nullable|string|max:100',
            'instruction' => 'nullable|string',
            'has_zone_id' => 'nullable|boolean',
            'zone_id_label' => 'nullable|string|max:50',
            'user_id_label' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Nama Kategori wajib diisi.',
            'slug.unique' => 'Slug kategori sudah digunakan.',
            'type.required' => 'Tipe Kategori wajib dipilih.',
            'status.required' => 'Status kategori wajib ditentukan.',
        ]);

        $validated['slug'] = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        $validated['has_zone_id'] = $request->boolean('has_zone_id');
        $validated['user_id_label'] = $validated['user_id_label'] ?: 'User ID';
        $validated['zone_id_label'] = $validated['zone_id_label'] ?: 'Zone ID';

        // Pastikan slug unik jika auto-generated
        $originalSlug = $validated['slug'];
        $count = 1;
        while (Category::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        $category = Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', "Kategori '{$category->name}' berhasil ditambahkan!");
    }

    /**
     * Form Edit Kategori
     */
    public function edit(Category $category): View
    {
        $types = ['games' => 'Game Online', 'pulsa' => 'Pulsa & Data', 'ewallet' => 'E-Wallet', 'pln' => 'Token PLN', 'voucher' => 'Voucher & TV'];
        return view('admin.categories.edit', compact('category', 'types'));
    }

    /**
     * Update Kategori
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:120|unique:categories,slug,' . $category->id,
            'type' => 'required|in:games,pulsa,ewallet,pln,voucher',
            'publisher' => 'nullable|string|max:100',
            'instruction' => 'nullable|string',
            'has_zone_id' => 'nullable|boolean',
            'zone_id_label' => 'nullable|string|max:50',
            'user_id_label' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Nama Kategori wajib diisi.',
            'slug.required' => 'Slug Kategori wajib diisi.',
            'slug.unique' => 'Slug kategori sudah digunakan oleh kategori lain.',
            'type.required' => 'Tipe Kategori wajib dipilih.',
            'status.required' => 'Status kategori wajib ditentukan.',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['has_zone_id'] = $request->boolean('has_zone_id');

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', "Kategori '{$category->name}' berhasil diperbarui!");
    }

    /**
     * Toggle Status Active / Inactive
     */
    public function toggleStatus(Category $category): RedirectResponse
    {
        $newStatus = $category->status === 'active' ? 'inactive' : 'active';
        $category->update(['status' => $newStatus]);

        $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Status kategori '{$category->name}' berhasil {$statusText}.");
    }

    /**
     * Hapus Kategori (Safe Delete Guard)
     */
    public function destroy(Category $category): RedirectResponse
    {
        $productCount = $category->products()->count();

        if ($productCount > 0) {
            return back()->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena masih memiliki {$productCount} produk terkait. Silakan nonaktifkan status kategori atau hapus/pindahkan produk terlebih dahulu.");
        }

        $categoryName = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', "Kategori '{$categoryName}' berhasil dihapus.");
    }
}
