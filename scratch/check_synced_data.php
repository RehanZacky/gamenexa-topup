<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Product;

echo "Total Categories: " . Category::count() . "\n";
echo "Total Products: " . Product::count() . "\n\n";

$categories = Category::withCount('products')->get();
foreach ($categories as $c) {
    if ($c->products_count > 0) {
        echo "- {$c->name} ({$c->type}): {$c->products_count} produk\n";
    }
}
