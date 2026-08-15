<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users (Admin & Customer)
        User::updateOrCreate(
            ['email' => 'admin@gamenexa.com'],
            [
                'name' => 'Admin GameNexa',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@gamenexa.com'],
            [
                'name' => 'Customer Demo',
                'phone' => '089876543210',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // 2. Seed Provider (Digiflazz)
        $digiflazz = Provider::updateOrCreate(
            ['code' => 'digiflazz'],
            [
                'name' => 'Digiflazz H2H Top-Up',
                'type' => 'h2h_api',
                'status' => 'active',
            ]
        );

        // 3. Seed Categories & Products
        $categoriesData = [
            [
                'name' => 'Mobile Legends',
                'slug' => 'mobile-legends',
                'type' => 'games',
                'publisher' => 'Moonton',
                'has_zone_id' => true,
                'user_id_label' => 'User ID',
                'zone_id_label' => 'Zone ID',
                'instruction' => 'Masukkan User ID (contoh: 12345678) dan Zone ID di dalam kurung (contoh: 1234).',
                'status' => 'active',
                'products' => [
                    ['name' => '86 Diamonds', 'sku' => 'ML86', 'modal' => 18000, 'sell' => 20000],
                    ['name' => '172 Diamonds', 'sku' => 'ML172', 'modal' => 36000, 'sell' => 39500],
                    ['name' => '257 Diamonds', 'sku' => 'ML257', 'modal' => 54000, 'sell' => 59000],
                    ['name' => '706 Diamonds', 'sku' => 'ML706', 'modal' => 145000, 'sell' => 158000],
                    ['name' => 'Weekly Diamond Pass', 'sku' => 'MLWDP', 'modal' => 27000, 'sell' => 29500],
                    ['name' => 'Twilight Pass', 'sku' => 'MLTP', 'modal' => 135000, 'sell' => 148000],
                ],
            ],
            [
                'name' => 'Free Fire',
                'slug' => 'free-fire',
                'type' => 'games',
                'publisher' => 'Garena',
                'has_zone_id' => false,
                'user_id_label' => 'Player ID',
                'instruction' => 'Masukkan Player ID Free Fire Anda yang tertera di profil game.',
                'status' => 'active',
                'products' => [
                    ['name' => '70 Diamonds', 'sku' => 'FF70', 'modal' => 9000, 'sell' => 10000],
                    ['name' => '140 Diamonds', 'sku' => 'FF140', 'modal' => 18000, 'sell' => 20000],
                    ['name' => '355 Diamonds', 'sku' => 'FF355', 'modal' => 45000, 'sell' => 49500],
                    ['name' => '720 Diamonds', 'sku' => 'FF720', 'modal' => 90000, 'sell' => 98000],
                    ['name' => 'Membership Mingguan', 'sku' => 'FFMW', 'modal' => 28000, 'sell' => 31000],
                    ['name' => 'Membership Bulanan', 'sku' => 'FFMM', 'modal' => 84000, 'sell' => 92000],
                ],
            ],
            [
                'name' => 'PUBG Mobile',
                'slug' => 'pubg-mobile',
                'type' => 'games',
                'publisher' => 'Tencent Games / Level Infinite',
                'has_zone_id' => false,
                'user_id_label' => 'Player ID',
                'instruction' => 'Masukkan Player ID PUBG Mobile Anda.',
                'status' => 'active',
                'products' => [
                    ['name' => '60 UC', 'sku' => 'PUBG60', 'modal' => 13500, 'sell' => 15000],
                    ['name' => '325 UC', 'sku' => 'PUBG325', 'modal' => 68000, 'sell' => 74500],
                    ['name' => '660 UC', 'sku' => 'PUBG660', 'modal' => 135000, 'sell' => 148000],
                ],
            ],
            [
                'name' => 'Genshin Impact',
                'slug' => 'genshin-impact',
                'type' => 'games',
                'publisher' => 'Hoyoverse',
                'has_zone_id' => true,
                'user_id_label' => 'UID',
                'zone_id_label' => 'Server',
                'instruction' => 'Masukkan UID Genshin Impact dan pilih Server game Anda (America, Europe, Asia, TW/HK/MO).',
                'status' => 'active',
                'products' => [
                    ['name' => '60 Genesis Crystals', 'sku' => 'GI60', 'modal' => 14500, 'sell' => 16000],
                    ['name' => '300+30 Genesis Crystals', 'sku' => 'GI330', 'modal' => 72000, 'sell' => 78500],
                    ['name' => 'Blessing of the Welkin Moon', 'sku' => 'GIWELKIN', 'modal' => 72000, 'sell' => 78000],
                ],
            ],
            [
                'name' => 'Valorant',
                'slug' => 'valorant',
                'type' => 'games',
                'publisher' => 'Riot Games',
                'has_zone_id' => false,
                'user_id_label' => 'Riot ID',
                'instruction' => 'Masukkan Riot ID lengkap beserta Tagline (contoh: Username#1234).',
                'status' => 'active',
                'products' => [
                    ['name' => '475 Points', 'sku' => 'VAL475', 'modal' => 50000, 'sell' => 55000],
                    ['name' => '1000 Points', 'sku' => 'VAL1000', 'modal' => 100000, 'sell' => 109000],
                    ['name' => '2050 Points', 'sku' => 'VAL2050', 'modal' => 200000, 'sell' => 218000],
                ],
            ],
        ];

        foreach ($categoriesData as $catItem) {
            $category = Category::updateOrCreate(
                ['slug' => $catItem['slug']],
                [
                    'name' => $catItem['name'],
                    'type' => $catItem['type'],
                    'publisher' => $catItem['publisher'],
                    'has_zone_id' => $catItem['has_zone_id'],
                    'user_id_label' => $catItem['user_id_label'] ?? 'User ID',
                    'zone_id_label' => $catItem['zone_id_label'] ?? 'Zone ID',
                    'instruction' => $catItem['instruction'],
                    'status' => $catItem['status'],
                ]
            );

            foreach ($catItem['products'] as $idx => $prodItem) {
                Product::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'provider_product_code' => $prodItem['sku'],
                    ],
                    [
                        'provider_id' => $digiflazz->id,
                        'name' => $prodItem['name'],
                        'slug' => Str::slug($category->name . ' ' . $prodItem['name']),
                        'modal_price' => $prodItem['modal'],
                        'selling_price' => $prodItem['sell'],
                        'status' => 'active',
                        'sort_order' => $idx + 1,
                    ]
                );
            }
        }
    }
}
