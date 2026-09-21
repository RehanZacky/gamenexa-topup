<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Provider;
use App\Services\DigiflazzService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Ambil daftar SKU & harga real-time Digiflazz berdasarkan kategori (AJAX Endpoint)
     */
    public function getDigiflazzSkus(Request $request, DigiflazzService $digiflazz): JsonResponse
    {
        $categoryId = $request->query('category_id');
        if (!$categoryId) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori ID wajib disertakan.',
            ], 400);
        }

        $category = Category::find($categoryId);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan.',
            ], 404);
        }

        $forceRefresh = $request->boolean('refresh');
        $cacheKey = 'digiflazz_pricelist_raw';

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        $items = Cache::remember($cacheKey, 300, function () use ($digiflazz) {
            $res = $digiflazz->getPriceList();
            return $res['data'] ?? [];
        });

        if (empty($items) || isset($items['rc'])) {
            $errMsg = $items['message'] ?? 'Tidak dapat terhubung ke server Digiflazz. Pastikan username dan API key sudah terkonfigurasi di file .env.';
            return response()->json([
                'success' => false,
                'message' => $errMsg,
            ], 502);
        }

        $catName = strtolower(trim($category->name));
        $catSlug = strtolower(trim($category->slug));
        $catClean = preg_replace('/[^a-z0-9]/', '', $catName);

        $matched = [];

        foreach ($items as $item) {
            if (!is_array($item) || empty($item['buyer_sku_code'])) {
                continue;
            }

            $brand = strtolower(trim($item['brand'] ?? ''));
            $brandSlug = Str::slug($brand);
            $brandClean = preg_replace('/[^a-z0-9]/', '', $brand);
            $prodName = strtolower(trim($item['product_name'] ?? ''));
            $prodNameClean = preg_replace('/[^a-z0-9]/', '', $prodName);

            $isMatch = false;

            if ($brandSlug === $catSlug || $brandClean === $catClean) {
                $isMatch = true;
            } elseif ($catClean && (str_contains($brandClean, $catClean) || str_contains($catClean, $brandClean))) {
                $isMatch = true;
            } elseif ($catClean && str_contains($prodNameClean, $catClean)) {
                $isMatch = true;
            }

            if ($isMatch) {
                $rawName = trim($item['product_name'] ?? $item['buyer_sku_code']);
                $brand = trim($item['brand'] ?? '');
                $catName = trim($category->name);

                // Pola regex pembersihan nama game/brand agar langsung mengarah ke nominal produk
                $stripPatterns = [
                    '/^' . preg_quote($catName, '/') . '\s*[-–:]?\s*/i',
                    '/^' . preg_quote($brand, '/') . '\s*[-–:]?\s*/i',
                    '/^(mobile\s*legends\s*(bang\s*bang)?|mlbb|garena\s*free\s*fire|free\s*fire|pubg\s*mobile|pubg|genshin\s*impact|valorant|point\s*blank|honkai\s*(star\s*rail)?|roblox|codm|call\s*of\s*duty)\s*[-–:]?\s*/i',
                ];

                $cleanName = $rawName;
                foreach ($stripPatterns as $pattern) {
                    $cleanName = preg_replace($pattern, '', $cleanName);
                }
                $cleanName = trim($cleanName, " \t\n\r\0\x0B-–:|");

                if (empty($cleanName)) {
                    $cleanName = $rawName;
                }

                $isActive = ($item['buyer_product_status'] ?? true) && ($item['seller_product_status'] ?? true);

                $matched[] = [
                    'sku' => $item['buyer_sku_code'],
                    'name' => $rawName,
                    'nominal' => $cleanName,
                    'clean_name' => $cleanName,
                    'price' => (float)($item['price'] ?? 0),
                    'brand' => $item['brand'] ?? '',
                    'category' => $item['category'] ?? '',
                    'is_active' => $isActive,
                    'desc' => $item['desc'] ?? '',
                ];
            }
        }

        usort($matched, fn($a, $b) => $a['price'] <=> $b['price']);

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'count' => count($matched),
            'products' => $matched,
        ]);
    }

    /**
     * Tampilkan Daftar Produk
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'provider']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('provider_product_code', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($catQ) use ($search) {
                      $catQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $products = $query->orderBy('category_id', 'asc')
                          ->orderBy('sort_order', 'asc')
                          ->orderBy('selling_price', 'asc')
                          ->paginate(20)
                          ->withQueryString();

        $categories = Category::orderBy('name', 'asc')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Form Tambah Produk
     */
    public function create(): View
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $providers = Provider::where('status', 'active')->get();

        return view('admin.products.create', compact('categories', 'providers'));
    }

    /**
     * Simpan Produk Baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'provider_id' => 'required|exists:providers,id',
            'provider_product_code' => 'required|string|max:100',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'modal_price' => 'nullable|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,cut_off',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'category_id.required' => 'Kategori wajib dipilih.',
            'provider_id.required' => 'Provider wajib dipilih.',
            'provider_product_code.required' => 'SKU / Provider Product Code wajib diisi.',
            'name.required' => 'Nama Produk wajib diisi.',
            'selling_price.required' => 'Harga Jual wajib diisi.',
        ]);

        $category = Category::findOrFail($validated['category_id']);
        $validated['slug'] = Str::slug($category->name . ' ' . $validated['name']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        // Harga modal paten terisi otomatis saat memilih SKU dari Digiflazz
        $validated['modal_price'] = $request->filled('modal_price') ? (float)$request->input('modal_price') : 0;

        $product = Product::create($validated);

        return redirect()->route('admin.products.index', ['category_id' => $product->category_id])
            ->with('success', "Produk '{$product->name}' berhasil ditambahkan!");
    }

    /**
     * Form Edit Produk
     */
    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $providers = Provider::all();
        $hasOrderHistory = $product->orderItems()->exists();

        return view('admin.products.edit', compact('product', 'categories', 'providers', 'hasOrderHistory'));
    }

    /**
     * Update Produk & Harga Jual
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'provider_id' => 'required|exists:providers,id',
            'provider_product_code' => 'required|string|max:100',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'selling_price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,cut_off',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'category_id.required' => 'Kategori wajib dipilih.',
            'provider_product_code.required' => 'SKU / Provider Product Code wajib diisi.',
            'name.required' => 'Nama Produk wajib diisi.',
            'selling_price.required' => 'Harga Jual wajib diisi.',
        ]);

        $category = Category::findOrFail($validated['category_id']);
        $validated['slug'] = Str::slug($category->name . ' ' . $validated['name']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        // Pertahankan harga modal asli dari Digiflazz (atau perbarui jika memilih SKU baru dari Digiflazz)
        $validated['modal_price'] = $request->filled('modal_price') ? (float)$request->input('modal_price') : $product->modal_price;

        $product->update($validated);

        return redirect()->route('admin.products.index', ['category_id' => $product->category_id])
            ->with('success', "Produk '{$product->name}' berhasil diperbarui!");
    }

    /**
     * Toggle Status Active / Inactive
     */
    public function toggleStatus(Product $product): RedirectResponse
    {
        $newStatus = $product->status === 'active' ? 'inactive' : 'active';
        $product->update(['status' => $newStatus]);

        $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Status produk '{$product->name}' berhasil {$statusText}.");
    }

    /**
     * Bulk Update Harga Jual Produk
     */
    public function bulkPriceUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'target_category_id' => 'nullable|exists:categories,id',
            'adjustment_type' => 'required|in:add_nominal,add_percent,set_margin,set_exact',
            'adjustment_value' => 'required|numeric',
        ], [
            'adjustment_type.required' => 'Jenis penyesuaian harga wajib dipilih.',
            'adjustment_value.required' => 'Nilai penyesuaian wajib diisi.',
        ]);

        $query = Product::query();

        if (!empty($validated['product_ids'])) {
            $query->whereIn('id', $validated['product_ids']);
        } elseif (!empty($validated['target_category_id'])) {
            $query->where('category_id', $validated['target_category_id']);
        } else {
            return back()->with('error', 'Silakan pilih minimal 1 produk atau pilih kategori target.');
        }

        $products = $query->get();
        $updatedCount = 0;
        $val = (float)$validated['adjustment_value'];

        foreach ($products as $prod) {
            $currentSelling = (float)$prod->selling_price;
            $modal = (float)$prod->modal_price;
            $newSelling = $currentSelling;

            switch ($validated['adjustment_type']) {
                case 'add_nominal':
                    $newSelling = max(0, $currentSelling + $val);
                    break;
                case 'add_percent':
                    $newSelling = max(0, round($currentSelling * (1 + ($val / 100))));
                    break;
                case 'set_margin':
                    $newSelling = max(0, $modal + $val);
                    break;
                case 'set_exact':
                    $newSelling = max(0, $val);
                    break;
            }

            $prod->update(['selling_price' => $newSelling]);
            $updatedCount++;
        }

        return back()->with('success', "Berhasil memperbarui harga jual untuk {$updatedCount} produk terpilih!");
    }

    /**
     * Hapus Produk (Safe Delete Guard)
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->orderItems()->exists()) {
            return back()->with('error', "Produk '{$product->name}' tidak dapat dihapus karena sudah memiliki riwayat transaksi order. Silakan nonaktifkan status produk menjadi 'Inactive' agar tidak tampil di katalog customer.");
        }

        $prodName = $product->name;
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', "Produk '{$prodName}' berhasil dihapus.");
    }
}
