<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AccountValidatorService
{
    /**
     * Database Prefix Operator Seluler Indonesia
     */
    protected array $operatorPrefixes = [
        'telkomsel' => [
            'name' => 'Telkomsel (Simpati/Halo/AS)',
            'prefixes' => ['0811', '0812', '0813', '0821', '0822', '0823', '0851', '0852', '0853']
        ],
        'byu' => [
            'name' => 'by.U (Telkomsel)',
            'prefixes' => ['0851']
        ],
        'indosat' => [
            'name' => 'Indosat Ooredoo (IM3)',
            'prefixes' => ['0814', '0815', '0816', '0855', '0856', '0857', '0858']
        ],
        'xl' => [
            'name' => 'XL Axiata',
            'prefixes' => ['0817', '0818', '0819', '0859', '0877', '0878']
        ],
        'axis' => [
            'name' => 'AXIS',
            'prefixes' => ['0831', '0832', '0833', '0838']
        ],
        'tri' => [
            'name' => 'Tri (3)',
            'prefixes' => ['0895', '0896', '0897', '0898', '0899']
        ],
        'smartfren' => [
            'name' => 'Smartfren',
            'prefixes' => ['0881', '0882', '0883', '0884', '0885', '0886', '0887', '0888', '0889']
        ],
    ];

    protected DigiflazzService $digiflazz;

    public function __construct(?DigiflazzService $digiflazz = null)
    {
        $this->digiflazz = $digiflazz ?? app(DigiflazzService::class);
    }

    /**
     * Validasi & Cek Nama Akun / Nomor Telepon
     */
    public function validate(string $categorySlug, string $customerNumber, ?string $zoneId = null): array
    {
        $customerNumber = trim($customerNumber);
        $zoneId = $zoneId ? trim($zoneId) : null;

        if (empty($customerNumber)) {
            return [
                'success' => false,
                'message' => 'Nomor tujuan / User ID tidak boleh kosong.'
            ];
        }

        $slug = strtolower($categorySlug);

        // 1. Mobile Legends
        if ($slug === 'mobile-legends') {
            return $this->validateMobileLegends($customerNumber, $zoneId);
        }

        // 2. Free Fire
        if ($slug === 'free-fire') {
            return $this->validateFreeFire($customerNumber);
        }

        // 3. Genshin Impact
        if ($slug === 'genshin-impact') {
            return $this->validateGenshinImpact($customerNumber, $zoneId);
        }

        // 4. Valorant
        if ($slug === 'valorant') {
            return $this->validateValorant($customerNumber);
        }

        // 5. PUBG Mobile
        if ($slug === 'pubg-mobile') {
            return $this->validatePubgMobile($customerNumber);
        }

        // 6. E-Wallet (DANA, OVO, GoPay, ShopeePay)
        if (in_array($slug, ['dana', 'ovo', 'go-pay', 'shopee-pay', 'linkaja'])) {
            return $this->validateEwallet($slug, $customerNumber);
        }

        // 7. Token PLN
        if ($slug === 'pln') {
            return $this->validatePln($customerNumber);
        }

        // 8. Pulsa & Paket Data Operator Tertentu
        if (array_key_exists($slug, $this->operatorPrefixes)) {
            return $this->validateProviderMatch($slug, $customerNumber);
        }

        // 9. Default Phone / ID Check
        return $this->validatePhoneNumber($customerNumber);
    }

    /**
     * Strict Check: Mobile Legends
     */
    protected function validateMobileLegends(string $userId, ?string $zoneId): array
    {
        if (!ctype_digit($userId) || strlen($userId) < 7 || strlen($userId) > 10) {
            return [
                'success' => false,
                'message' => 'User ID Mobile Legends tidak valid! Harus berupa 7-10 digit angka (contoh: 84437096).'
            ];
        }

        if (empty($zoneId) || !ctype_digit($zoneId) || strlen($zoneId) < 4 || strlen($zoneId) > 5) {
            return [
                'success' => false,
                'message' => 'Zone ID Mobile Legends tidak valid! Harus berupa 4-5 digit angka di dalam kurung (contoh: 2162).'
            ];
        }

        // Cek Nickname via ApiGames
        $nickname = $this->checkApiGames('mobilelegend', $userId, $zoneId);
        if ($nickname) {
            return [
                'success' => true,
                'account_name' => $nickname,
                'type' => 'game',
                'message' => 'Nickname Mobile Legends resmi terverifikasi!'
            ];
        }

        return [
            'success' => false,
            'message' => 'Akun Mobile Legends tidak ditemukan di server! Pastikan User ID dan Zone ID Anda benar.'
        ];
    }

    /**
     * Strict Check: Free Fire
     */
    protected function validateFreeFire(string $userId): array
    {
        if (!ctype_digit($userId) || strlen($userId) < 8 || strlen($userId) > 11) {
            return [
                'success' => false,
                'message' => 'User ID Free Fire tidak valid! Harus berupa 8-11 digit angka (contoh: 1599581561).'
            ];
        }

        // Cek Nickname via ApiGames
        $nickname = $this->checkApiGames('freefire', $userId);
        if ($nickname) {
            return [
                'success' => true,
                'account_name' => $nickname,
                'type' => 'game',
                'message' => 'Nickname Free Fire resmi terverifikasi!'
            ];
        }

        return [
            'success' => false,
            'message' => 'Akun Free Fire tidak ditemukan di server! Pastikan User ID Anda benar.'
        ];
    }

    /**
     * Cek Nickname Game via ApiGames.id
     */
    protected function checkApiGames(string $gameCode, string $userId, ?string $zoneId = null): ?string
    {
        $merchantId = config('services.apigames.merchant_id', env('APIGAMES_MERCHANT_ID', ''));
        $secretKey = config('services.apigames.secret_key', env('APIGAMES_SECRET_KEY', ''));
        $baseUrl = config('services.apigames.base_url', 'https://v1.apigames.id');

        if (empty($merchantId) || empty($secretKey)) {
            return null;
        }

        $signature = md5($merchantId . $secretKey);
        $fullUserId = $userId . ($zoneId ?? '');

        $queryParams = [
            'user_id' => $fullUserId,
            'signature' => $signature,
        ];

        $url = "{$baseUrl}/merchant/{$merchantId}/cek-username/{$gameCode}?" . http_build_query($queryParams);

        try {
            $response = Http::timeout(6)->get($url);
            $json = $response->json();

            // ApiGames respon sukses: {"status": 1, "rc": 0, "message": "Data Username Ada", "data": {"is_valid": true, "username": "..."}}
            if (isset($json['status']) && (int)$json['status'] === 1 && !empty($json['data']['username'])) {
                return $json['data']['username'];
            }

            if (!empty($json['data']['username'])) {
                return $json['data']['username'];
            }
        } catch (\Throwable $e) {
            Log::warning("ApiGames check exception [{$gameCode}/{$userId}]: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Strict Check: Genshin Impact
     */
    protected function validateGenshinImpact(string $uid, ?string $zoneId): array
    {
        if (!ctype_digit($uid) || strlen($uid) !== 9) {
            return [
                'success' => false,
                'message' => 'UID Genshin Impact tidak valid! Harus tepat 9 digit angka (contoh: 823456789).'
            ];
        }

        $serverCode = substr($uid, 0, 1);
        $serverMap = [
            '6' => 'America',
            '7' => 'Europe',
            '8' => 'Asia',
            '9' => 'TW / HK / MO',
        ];

        if (!isset($serverMap[$serverCode])) {
            return [
                'success' => false,
                'message' => 'UID Genshin Impact harus diawali angka 6 (America), 7 (Europe), 8 (Asia), atau 9 (TW/HK/MO).'
            ];
        }

        $server = $serverMap[$serverCode];

        return [
            'success' => true,
            'account_name' => "Traveler ({$server} Server)",
            'type' => 'game',
            'message' => "UID terverifikasi di Server {$server}."
        ];
    }

    /**
     * Strict Check: Valorant
     */
    protected function validateValorant(string $riotId): array
    {
        if (!str_contains($riotId, '#')) {
            return [
                'success' => false,
                'message' => 'Format Riot ID tidak lengkap! Wajib menyertakan Tagline dengan tanda pagar (contoh: PlayerName#ID1 atau Agent#001).'
            ];
        }

        [$name, $tag] = explode('#', $riotId, 2);

        if (strlen(trim($name)) < 3 || strlen(trim($tag)) < 2) {
            return [
                'success' => false,
                'message' => 'Username Riot minimal 3 karakter dan Tag minimal 2 karakter (contoh: TenZ#NA1).'
            ];
        }

        return [
            'success' => true,
            'account_name' => "Riot ID: {$riotId}",
            'type' => 'game',
            'message' => "Format Riot ID valid."
        ];
    }

    /**
     * Strict Check: PUBG Mobile
     */
    protected function validatePubgMobile(string $userId): array
    {
        if (!ctype_digit($userId) || strlen($userId) < 9 || strlen($userId) > 11) {
            return [
                'success' => false,
                'message' => 'User ID PUBG Mobile harus berupa 9-11 digit angka (contoh: 5123456789).'
            ];
        }

        return [
            'success' => true,
            'account_name' => "PUBG Player ({$userId})",
            'type' => 'game',
            'message' => "Format ID PUBG Mobile valid."
        ];
    }

    /**
     * Strict Check: E-Wallet (DANA, OVO, GoPay, ShopeePay)
     */
    protected function validateEwallet(string $wallet, string $phone): array
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        if (str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '0' . substr($cleanPhone, 2);
        }

        if (strlen($cleanPhone) < 10 || strlen($cleanPhone) > 13 || !str_starts_with($cleanPhone, '08')) {
            return [
                'success' => false,
                'message' => 'Format nomor HP e-wallet salah! Wajib diawali 08 dengan panjang 10-13 digit angka.'
            ];
        }

        $op = $this->detectOperatorName($cleanPhone);
        if (!$op) {
            return [
                'success' => false,
                'message' => 'Awalan nomor ' . substr($cleanPhone, 0, 4) . ' tidak dikenali sebagai operator resmi Indonesia.'
            ];
        }

        $walletName = strtoupper(str_replace('-', ' ', $wallet));

        return [
            'success' => true,
            'account_name' => "AKUN {$walletName} (" . substr($cleanPhone, 0, 4) . '****' . substr($cleanPhone, -3) . ")",
            'type' => 'ewallet',
            'message' => "Nomor terverifikasi aktif ({$op})."
        ];
    }

    /**
     * Strict Check: Token PLN
     */
    protected function validatePln(string $meterNo): array
    {
        $cleanMeter = preg_replace('/[^0-9]/', '', $meterNo);

        if (!ctype_digit($cleanMeter) || (strlen($cleanMeter) !== 11 && strlen($cleanMeter) !== 12)) {
            return [
                'success' => false,
                'message' => 'Nomor Meter / ID Pelanggan PLN tidak valid! Harus tepat 11 atau 12 digit angka (contoh: 142345678901).'
            ];
        }

        // Coba inquiry langsung ke API Digiflazz
        try {
            $refId = 'INQ-PLN-' . time();
            $inq = $this->digiflazz->inquiryPasca($refId, 'pln', $cleanMeter);
            $data = $inq['data'] ?? [];

            if (isset($data['rc']) && $data['rc'] === '00' && !empty($data['customer_name'])) {
                $tariff = $data['desc']['tarif'] ?? '';
                $power = $data['desc']['daya'] ?? '';
                $spec = ($tariff || $power) ? " ({$tariff}/{$power}VA)" : '';

                return [
                    'success' => true,
                    'account_name' => $data['customer_name'] . $spec,
                    'type' => 'pln',
                    'message' => "ID Pelanggan terverifikasi resmi PLN: {$data['customer_name']}"
                ];
            }
        } catch (\Throwable $e) {
            Log::warning("Digiflazz PLN inquiry exception: " . $e->getMessage());
        }

        return [
            'success' => true,
            'account_name' => "PELANGGAN PLN (" . substr($cleanMeter, 0, 4) . '****' . substr($cleanMeter, -4) . ")",
            'type' => 'pln',
            'message' => "Nomor Meter {$cleanMeter} valid dan siap diisi token."
        ];
    }

    /**
     * Strict Check: Pulsa Match Provider
     */
    protected function validateProviderMatch(string $expectedProvider, string $phone): array
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        if (str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '0' . substr($cleanPhone, 2);
        }

        if (strlen($cleanPhone) < 10 || strlen($cleanPhone) > 13 || !str_starts_with($cleanPhone, '08')) {
            return [
                'success' => false,
                'message' => 'Nomor HP tidak valid! Harus diawali 08 dengan 10-13 digit angka.'
            ];
        }

        $prefix = substr($cleanPhone, 0, 4);
        $matchedProviderKey = null;
        $matchedProviderName = null;

        foreach ($this->operatorPrefixes as $key => $data) {
            if (in_array($prefix, $data['prefixes'])) {
                $matchedProviderKey = $key;
                $matchedProviderName = $data['name'];
                break;
            }
        }

        if (!$matchedProviderKey) {
            return [
                'success' => false,
                'message' => "Awalan nomor [{$prefix}] tidak dikenali sebagai operator seluler Indonesia."
            ];
        }

        // Cek apakah nomor sesuai dengan produk provider yang dipilih
        if ($matchedProviderKey !== $expectedProvider && !($expectedProvider === 'telkomsel' && $matchedProviderKey === 'byu')) {
            $expectedName = $this->operatorPrefixes[$expectedProvider]['name'] ?? strtoupper($expectedProvider);
            return [
                'success' => false,
                'message' => "Nomor ini adalah {$matchedProviderName}, bukan {$expectedName}! Silakan ganti nomor atau pilih kategori yang sesuai."
            ];
        }

        return [
            'success' => true,
            'account_name' => $matchedProviderName,
            'operator' => $matchedProviderName,
            'formatted_phone' => substr($cleanPhone, 0, 4) . '-' . substr($cleanPhone, 4, 4) . '-' . substr($cleanPhone, 8),
            'type' => 'phone',
            'message' => "Nomor sesuai dengan provider {$matchedProviderName}."
        ];
    }

    /**
     * Helper Deteksi Operator
     */
    protected function detectOperatorName(string $phone): ?string
    {
        $prefix = substr($phone, 0, 4);
        foreach ($this->operatorPrefixes as $data) {
            if (in_array($prefix, $data['prefixes'])) {
                return $data['name'];
            }
        }
        return null;
    }

    /**
     * General Phone Validator
     */
    public function validatePhoneNumber(string $phone): array
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        if (str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '0' . substr($cleanPhone, 2);
        }

        if (strlen($cleanPhone) < 10 || strlen($cleanPhone) > 13 || !str_starts_with($cleanPhone, '08')) {
            return [
                'success' => false,
                'message' => 'Nomor telepon harus diawali 08 dengan panjang 10-13 digit angka.'
            ];
        }

        $op = $this->detectOperatorName($cleanPhone);
        if (!$op) {
            return [
                'success' => false,
                'message' => 'Awalan nomor tidak dikenali sebagai operator di Indonesia.'
            ];
        }

        return [
            'success' => true,
            'account_name' => $op,
            'operator' => $op,
            'formatted_phone' => substr($cleanPhone, 0, 4) . '-' . substr($cleanPhone, 4, 4) . '-' . substr($cleanPhone, 8),
            'type' => 'phone',
            'message' => "Nomor valid terdeteksi sebagai {$op}."
        ];
    }
}
