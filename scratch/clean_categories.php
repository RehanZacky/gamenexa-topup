<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;

// Hapus kategori kosong jika ada
Category::whereDoesntHave('products')->delete();

// Update type dan info kategori
$updates = [
    'dana' => ['type' => 'ewallet', 'user_id_label' => 'Nomor HP DANA', 'instruction' => 'Masukkan nomor handphone yang terdaftar di akun DANA.'],
    'ovo' => ['type' => 'ewallet', 'user_id_label' => 'Nomor HP OVO', 'instruction' => 'Masukkan nomor handphone yang terdaftar di akun OVO.'],
    'go-pay' => ['type' => 'ewallet', 'user_id_label' => 'Nomor HP GoPay', 'instruction' => 'Masukkan nomor handphone yang terdaftar di akun GoPay.'],
    'shopee-pay' => ['type' => 'ewallet', 'user_id_label' => 'Nomor HP ShopeePay', 'instruction' => 'Masukkan nomor handphone yang terdaftar di akun ShopeePay.'],
    'pln' => ['type' => 'pln', 'user_id_label' => 'No. Meter / ID Pelanggan', 'instruction' => 'Masukkan 11-12 digit Nomor Meteran atau ID Pelanggan PLN Anda.'],
    'k-vision-dan-gol' => ['type' => 'voucher', 'user_id_label' => 'Nomor Pelanggan / Smartcard', 'instruction' => 'Masukkan nomor Smartcard / ID Pelanggan K-Vision.'],
    'pertamina-gas' => ['type' => 'voucher', 'user_id_label' => 'Nomor Pelanggan / Meter', 'instruction' => 'Masukkan nomor ID Pelanggan Pertamina Gas.'],
];

foreach ($updates as $slug => $data) {
    Category::where('slug', $slug)->update($data);
}

echo "Categories cleaned and properly classified!\n";
