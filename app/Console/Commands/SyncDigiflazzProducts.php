<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\Provider;
use App\Services\DigiflazzService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncDigiflazzProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'digiflazz:sync-products {--category= : Filter by brand / category name} {--margin=2000 : Default profit margin in Rupiah} {--wipe : Reset all existing products and sync cleanly}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi daftar produk dan harga langsung dari API Digiflazz';

    /**
     * Execute the console command.
     */
    public function handle(DigiflazzService $digiflazz)
    {
        $this->info('Mengambil data produk dari Digiflazz API...');

        if ($this->option('wipe')) {
            $this->warn('Menghapus data produk lama sebelum sinkronisasi...');
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
            Product::truncate();
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        }

        $response = $digiflazz->getPriceList();
        $items = $response['data'] ?? [];

        if (empty($items) || isset($items['rc'])) {
            $msg = $items['message'] ?? 'Tidak ada data produk yang diterima dari Digiflazz.';
            $this->error("Gagal mengambil produk: {$msg}");
            return 1;
        }

        $provider = Provider::firstOrCreate(
            ['code' => 'digiflazz'],
            ['name' => 'Digiflazz H2H Top-Up', 'type' => 'h2h_api', 'status' => 'active']
        );

        $filterCategory = $this->option('category');
        $margin = (float)$this->option('margin');
        $syncedCount = 0;

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

            // Tentukan type dan label kategori
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

            // Kategori
            $category = Category::updateOrCreate(
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
            $sellingPrice = $modalPrice + $margin;
            $isBuyerProductStatus = ($item['buyer_product_status'] ?? true) && ($item['seller_product_status'] ?? true);

            Product::updateOrCreate(
                [
                    'category_id' => $category->id,
                    'provider_product_code' => $item['buyer_sku_code'],
                ],
                [
                    'provider_id' => $provider->id,
                    'name' => $item['product_name'] ?? $item['buyer_sku_code'],
                    'slug' => Str::slug($category->name . ' ' . ($item['product_name'] ?? $item['buyer_sku_code'])),
                    'description' => $item['desc'] ?? null,
                    'modal_price' => $modalPrice,
                    'selling_price' => $sellingPrice,
                    'status' => $isBuyerProductStatus ? 'active' : 'inactive',
                ]
            );

            $syncedCount++;
        }

        $this->info("Berhasil melakukan sinkronisasi {$syncedCount} produk Digiflazz!");
        return 0;
    }
}
