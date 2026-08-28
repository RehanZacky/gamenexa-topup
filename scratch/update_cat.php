<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\Category::where('slug', 'pertamina-gas')->update(['type' => 'voucher']);
echo "Updated pertamina-gas type to voucher successfully.\n";
