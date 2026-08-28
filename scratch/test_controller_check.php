<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/api/check-account', 'POST', [
    'category_slug' => 'mobile-legends',
    'customer_number' => '12345',
    'zone_id' => '12'
]);

$controller = new App\Http\Controllers\AccountValidationController();
$validator = new App\Services\AccountValidatorService();

$response = $controller->check($request, $validator);
echo "Response for fake ID (12345 / 12):\n";
echo $response->getContent() . "\n\n";

$requestValid = Illuminate\Http\Request::create('/api/check-account', 'POST', [
    'category_slug' => 'mobile-legends',
    'customer_number' => '84437096',
    'zone_id' => '2162'
]);
$responseValid = $controller->check($requestValid, $validator);
echo "Response for valid ID (84437096 / 2162):\n";
echo $responseValid->getContent() . "\n";
