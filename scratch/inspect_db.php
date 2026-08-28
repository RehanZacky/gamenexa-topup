<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$categories = App\Models\Category::withCount('products')->get();
echo "TOTAL CATEGORIES: " . $categories->count() . "\n\n";

foreach ($categories as $cat) {
    echo "ID: {$cat->id} | Name: {$cat->name} | Type: {$cat->type} | Slug: {$cat->slug} | Products: {$cat->products_count}\n";
    $products = $cat->products()->take(3)->get();
    foreach ($products as $p) {
        echo "   -> [{$p->provider_product_code}] {$p->name} | Modal: Rp" . number_format($p->modal_price, 0, ',', '.') . " | Jual: Rp" . number_format($p->selling_price, 0, ',', '.') . " | Status: {$p->status}\n";
    }
}
