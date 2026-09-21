<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Provider;
use App\Services\DigiflazzService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DigiflazzController extends Controller
{
    /**
     * Tampilkan Halaman Integrasi Digiflazz
     */
    public function index(DigiflazzService $digiflazz): View
    {
        $balanceData = $digiflazz->checkBalance();
        $balance = $balanceData['data']['deposit'] ?? 0;
        $balanceRc = $balanceData['data']['rc'] ?? null;
        $balanceMsg = $balanceData['data']['message'] ?? 'Connected';

        $totalProducts = Product::whereHas('provider', function ($q) {
            $q->where('code', 'digiflazz');
        })->count();

        $activeProducts = Product::whereHas('provider', function ($q) {
            $q->where('code', 'digiflazz');
        })->where('status', 'active')->count();

        $lastSync = Product::whereHas('provider', function ($q) {
            $q->where('code', 'digiflazz');
        })->latest('updated_at')->first()?->updated_at;

        $categories = Category::orderBy('name', 'asc')->get();

        return view('admin.digiflazz.index', compact(
            'balance',
            'balanceRc',
            'balanceMsg',
            'totalProducts',
            'activeProducts',
            'lastSync',
            'categories'
        ));
    }

    /**
     * Jalankan Sinkronisasi Produk & Harga Digiflazz
     */
    public function sync(Request $request, DigiflazzService $digiflazz): RedirectResponse
    {
        $validated = $request->validate([
            'category_filter' => 'nullable|string',
            'default_margin' => 'required|numeric|min:0',
            'update_selling_price' => 'nullable|boolean',
        ]);

        $margin = (float)$validated['default_margin'];
        $filterCategory = $validated['category_filter'] ?? null;
        $shouldUpdateSellingPrice = $request->boolean('update_selling_price');

        $response = $digiflazz->getPriceList();
        $items = $response['data'] ?? [];

        if (empty($items) || isset($items['rc'])) {
            $errMsg = $items['message'] ?? 'Gagal menghubungi API Digiflazz. Periksa username dan key di pengaturan environment.';
            return back()->with('error', "Gagal Sync Digiflazz: {$errMsg}");
        }

        $provider = Provider::firstOrCreate(
            ['code' => 'digiflazz'],
            ['name' => 'Digiflazz H2H Top-Up', 'type' => 'h2h_api', 'status' => 'active']
        );

        $syncedCount = 0;
        $newCount = 0;
        $updatedCount = 0;

        foreach ($items as $item) {
            if (!is_array($item) || empty($item['buyer_sku_code'])) {
                continue;
            }

            $brand = $item['brand'] ?? 'Games';
            $categoryName = strtolower($item['category'] ?? 'games');
            $slug = Str::slug($brand);

            if ($filterCategory && stripos($brand, $filterCategory) === false) {
                continue;
            }

            // Tentukan type dan label kategori jika baru dibuat
            $type = 'games';
            $userIdLabel = 'User ID';
            $zoneIdLabel = 'Zone ID';
            $hasZoneId = in_array($slug, ['mobile-legends', 'genshin-impact']);
            $instruction = 'Masukkan User ID akun game Anda dengan benar.';

            if (in_array($slug, ['dana', 'ovo', 'go-pay', 'shopee-pay', 'linkaja'])) {
                $type = 'ewallet';
                $userIdLabel = 'Nomor HP Akun';
                $instruction = 'Masukkan nomor HP yang terdaftar di akun ' . $brand . ' Anda.';
            } elseif ($slug === 'pln') {
                $type = 'pln';
                $userIdLabel = 'No. Meter / ID Pelanggan';
                $instruction = 'Masukkan 11-12 digit Nomor Meteran atau ID Pelanggan PLN Anda.';
            } elseif (in_array($slug, ['telkomsel', 'xl', 'axis', 'tri', 'indosat', 'smartfren', 'byu'])) {
                $type = 'pulsa';
                $userIdLabel = 'Nomor Handphone';
                $instruction = 'Masukkan nomor handphone ' . $brand . ' tujuan (contoh: 081234567890).';
            } elseif (in_array($categoryName, ['voucher', 'streaming', 'tv']) || in_array($slug, ['pertamina-gas', 'k-vision-dan-gol'])) {
                $type = 'voucher';
                $userIdLabel = 'Nomor HP / ID Pelanggan';
                $instruction = 'Masukkan nomor HP atau ID pelanggan untuk menerima kode/paket voucher.';
            }

            $category = Category::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $brand,
                    'type' => $type,
                    'publisher' => $brand,
                    'has_zone_id' => $hasZoneId,
                    'zone_id_label' => $zoneIdLabel,
                    'user_id_label' => $userIdLabel,
                    'instruction' => $instruction,
                    'status' => 'active',
                ]
            );

            $modalPrice = (float)($item['price'] ?? 0);
            $isBuyerProductStatus = ($item['buyer_product_status'] ?? true) && ($item['seller_product_status'] ?? true);

            $existingProduct = Product::where('category_id', $category->id)
                ->where('provider_product_code', $item['buyer_sku_code'])
                ->first();

            if ($existingProduct) {
                $updateData = [
                    'provider_id' => $provider->id,
                    'name' => $item['product_name'] ?? $item['buyer_sku_code'],
                    'modal_price' => $modalPrice,
                    'description' => $item['desc'] ?? $existingProduct->description,
                ];

                // Hanya ubah harga jual jika admin mencentang opsi penyesuaian harga jual
                if ($shouldUpdateSellingPrice) {
                    $updateData['selling_price'] = $modalPrice + $margin;
                }

                // Jika status dari provider cut_off/gangguan
                if (!$isBuyerProductStatus && $existingProduct->status === 'active') {
                    $updateData['status'] = 'inactive';
                }

                $existingProduct->update($updateData);
                $updatedCount++;
            } else {
                Product::create([
                    'category_id' => $category->id,
                    'provider_id' => $provider->id,
                    'provider_product_code' => $item['buyer_sku_code'],
                    'name' => $item['product_name'] ?? $item['buyer_sku_code'],
                    'slug' => Str::slug($category->name . ' ' . ($item['product_name'] ?? $item['buyer_sku_code'])),
                    'description' => $item['desc'] ?? null,
                    'modal_price' => $modalPrice,
                    'selling_price' => $modalPrice + $margin,
                    'status' => $isBuyerProductStatus ? 'active' : 'inactive',
                ]);
                $newCount++;
            }

            $syncedCount++;
        }

        return redirect()->route('admin.digiflazz.index')->with(
            'success',
            "Sinkronisasi berhasil! Total {$syncedCount} produk diproses ({$newCount} produk baru ditambahkan, {$updatedCount} harga modal produk diperbarui)."
        );
    }
}
