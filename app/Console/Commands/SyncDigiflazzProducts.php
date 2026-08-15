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
    protected $signature = 'digiflazz:sync-products {--category= : Filter by brand / category name} {--margin=2000 : Default profit margin in Rupiah}';

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

        $response = $digiflazz->getPriceList();
        $items = $response['data'] ?? [];

        if (empty($items)) {
            $this->error('Tidak ada data produk yang diterima dari Digiflazz. Pastikan username dan key di .env sudah benar.');
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
            $brand = $item['brand'] ?? 'Games';
            $categoryName = $item['category'] ?? 'Games';

            if ($filterCategory && stripos($brand, $filterCategory) === false) {
                continue;
            }

            // Kategori Game
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($brand)],
                [
                    'name' => $brand,
                    'type' => strtolower($categoryName) === 'games' ? 'games' : 'pulsa',
                    'publisher' => $brand,
                    'has_zone_id' => in_array(Str::slug($brand), ['mobile-legends', 'genshin-impact']),
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
