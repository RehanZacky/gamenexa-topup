# 🚀 PANDUAN DEPLOYMENT KE SERVER PRODUCTION (GAMENEXA TOP-UP)

Dokumen ini ditujukan untuk **Tim SysAdmin / DevOps / Tim Production** sebagai panduan lengkap langkah demi langkah (*step-by-step*) saat memindahkan aplikasi GameNexa Top-Up dari lingkungan *development* ke server *production* (VPS/Cloud Server).

---

## 📋 1. SPESIFIKASI & KEBUTUHAN SERVER (SERVER REQUIREMENTS)

Pastikan server production memenuhi spesifikasi berikut:

* **Operating System**: Linux (Ubuntu 22.04 / 24.04 LTS atau Debian 11/12 direkomendasikan).
* **Web Server**: Nginx (direkomendasikan) atau Apache dengan modul `mod_rewrite` aktif.
* **PHP**: **PHP >= 8.2** (Rekomendasi: PHP 8.2 / 8.3 / 8.4).
  * **PHP Extensions Wajib**:
    * `php-fpm`
    * `php-mysql` / `php-pdo`
    * `php-curl`
    * `php-mbstring`
    * `php-openssl`
    * `php-xml`
    * `php-bcmath`
    * `php-fileinfo`
    * `php-tokenizer`
    * `php-zip`
* **Database**: MySQL >= 8.0 atau MariaDB >= 10.4.
* **SSL Certificate**: **Wajib HTTPS Aktif** (Let's Encrypt / Cloudflare SSL) untuk menerima Webhook Midtrans & Digiflazz.
* **Static Public IP Address**: Wajib memiliki IP Publik Statis untuk didaftarkan (*whitelist*) di dashboard Digiflazz.
* **Tools Tambahan**: `Composer` >= 2.5, `Git`, `Supervisor` (opsional untuk background queue).

---

## ⚙️ 2. CHECKLIST VARIABEL ENVIRONMENT PRODUCTION (`.env`)

Salin file `.env.example` menjadi `.env` pada server production dan pastikan parameter berikut terkonfigurasi dengan benar:

```ini
APP_NAME=GameNexa
APP_ENV=production
APP_KEY=                        # Generate via: php artisan key:generate
APP_DEBUG=false                 # WAJIB false di server production
APP_URL=https://domain-anda.com  # Ganti dengan domain production resmi (HTTPS)

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# 1. DATABASE CONFIGURATION
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gamenexa_production
DB_USERNAME=gamenexa_user
DB_PASSWORD=password_database_rahasia_anda

# 2. DIGIFLAZZ PRODUCTION CONFIGURATION
DIGIFLAZZ_USERNAME=wedeguD2KMJW
DIGIFLAZZ_KEY=b263fbbf-8193-5ce9-a8ab-a0e7b6b82118    # Kunci Production Digiflazz
DIGIFLAZZ_MODE=production                             # Ubah ke mode 'production'
DIGIFLAZZ_WEBHOOK_SECRET=                             # Secret key dari menu Webhook Digiflazz

# 3. APIGAMES VALIDATOR CONFIGURATION (CEK NICKNAME REAL-TIME)
APIGAMES_MERCHANT_ID=M260828ITYJ8730BZ
APIGAMES_SECRET_KEY=ccae5d6a63a0a129f8cbab93f385833adccf66a2dd1cf041f0c9dac7ed67a8c1
APIGAMES_BASE_URL=https://v1.apigames.id

# 4. MIDTRANS PAYMENT GATEWAY (PRODUCTION)
MIDTRANS_SERVER_KEY=Mid-server-xxxxxxxxxxxx            # Server Key Production dari Dashboard Midtrans
MIDTRANS_CLIENT_KEY=Mid-client-xxxxxxxxxxxx            # Client Key Production dari Dashboard Midtrans
MIDTRANS_IS_PRODUCTION=true                            # WAJIB diubah ke true
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true

# 5. SESSION & CACHE CONFIGURATION
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database
```

---

## 🛠️ 3. LANGKAH-LANGKAH DEPLOYMENT (STEP-BY-STEP)

Jalankan perintah berikut di terminal server:

### **Langkah 1: Clone Repository & Pindah ke Direktori Project**
```bash
cd /var/www
git clone <URL_REPOSITORY_GIT> gamenexa-topup
cd gamenexa-topup
```

### **Langkah 2: Set Permission Folder Wajib**
Web server (`www-data` atau `nginx`) wajib memiliki hak akses tulis ke folder `storage` dan `bootstrap/cache`:
```bash
sudo chown -R www-data:www-data /var/www/gamenexa-topup
sudo chmod -R 775 /var/www/gamenexa-topup/storage
sudo chmod -R 775 /var/www/gamenexa-topup/bootstrap/cache
```

### **Langkah 3: Install PHP Dependencies (Composer No-Dev)**
```bash
composer install --no-dev --optimize-autoloader
```

### **Langkah 4: Konfigurasi Environment & Generate App Key**
```bash
cp .env.example .env
# Edit .env sesuai checklist di Bagian 2
nano .env

# Generate Application Key
php artisan key:generate
```

### **Langkah 5: Jalankan Database Migration**
```bash
php artisan migrate --force
```

### **Langkah 6: Sinkronisasi Awal Produk dari Digiflazz Production**
Tarik katalog produk resmi yang sudah diatur di akun Digiflazz ke database:
```bash
php artisan digiflazz:sync-products --wipe
```

### **Langkah 7: Optimasi Kecepatan Cache Laravel (Production Mode)**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🌐 4. PENGATURAN WEB SERVER (NGINX CONFIGURATION)

Pastikan `root` mengarah ke folder `/public`:

```nginx
server {
    listen 80;
    server_name domain-anda.com www.domain-anda.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name domain-anda.com www.domain-anda.com;

    root /var/www/gamenexa-topup/public;
    index index.php index.html;

    # SSL Certificate (Certbot Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/domain-anda.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/domain-anda.com/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock; # Sesuaikan dengan versi PHP server
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 🔗 5. PENGATURAN DI DASHBOARD PIHAK KETIGA (DIGIFLAZZ & MIDTRANS)

### **A. Pengaturan di Dashboard Digiflazz (`member.digiflazz.com`):**
1. **Daftarkan IP Server (Whitelist IP)**:
   - Masuk ke menu **Pengaturan Akun / Atur Koneksi / IP Whitelist**.
   - Masukkan **Static IP Public Server Production** Anda.
   - *Catatan: Tanpa whitelist IP, transaksi Digiflazz akan ditolak dengan kode error IP Unrecognized.*
2. **Atur Webhook URL Digiflazz**:
   - Masuk ke menu **Atur Koneksi ➡️ Webhook**.
   - Masukkan URL: `https://domain-anda.com/webhook/digiflazz`
   - Simpan **Secret Key Webhook** dan masukkan ke variable `DIGIFLAZZ_WEBHOOK_SECRET` di `.env`.

### **B. Pengaturan di Dashboard Midtrans (`dashboard.midtrans.com`):**
1. **Ubah Environment ke Mode Production**.
2. Masuk ke menu **Settings ➡️ Configuration**:
   - **Payment Notification URL**: `https://domain-anda.com/webhook/midtrans`
   - **Finish Redirect URL**: `https://domain-anda.com/order/status`
   - **Unfinish Redirect URL**: `https://domain-anda.com/order/status`
   - **Error Redirect URL**: `https://domain-anda.com/order/status`

---

## ⏰ 6. CRON JOB SCHEDULER (OTOMATISASI SERVER)

Agar fitur pembaruan harga Digiflazz, sinkronisasi berkala, dan pengecekan transaksi otomatis berjalan setiap jam, tambahkan Laravel Scheduler di crontab server:

Buka crontab dengan user `www-data` atau `root`:
```bash
sudo crontab -e -u www-data
```

Tambahkan baris berikut di baris paling bawah:
```cron
* * * * * cd /var/www/gamenexa-topup && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🛡️ 7. CHECKLIST PEMERIKSAAN AKHIR (GO-LIVE CHECKLIST)

Sebelum website dibuka untuk publik, tim production wajib memvalidasi:

- [ ] `APP_DEBUG` bernilai `false` di file `.env`.
- [ ] Sertifikat SSL (HTTPS) sudah aktif dan tidak ada peringatan *Mixed Content*.
- [ ] IP Server Production sudah di-whitelist di dashboard Digiflazz.
- [ ] Kredensial Digiflazz menggunakan **Production Key** (`b263fbbf-8193-5ce9-a8ab-a0e7b6b82118`) dan `DIGIFLAZZ_MODE=production`.
- [ ] Saldo akun Digiflazz mencukupi untuk memproses transaksi.
- [ ] Kredensial Midtrans menggunakan **Server Key & Client Key Production** dan `MIDTRANS_IS_PRODUCTION=true`.
- [ ] Cron Job scheduler sudah berjalan aktif.
- [ ] Cek Nickname Game (Mobile Legends & Free Fire via ApiGames) berhasil memunculkan username pemain.
- [ ] Melakukan 1 transaksi uji coba pembayaran asli (misal: nominal terkecil pulsa/diamond) untuk memverifikasi alur otomatisasi pembayaran ➡️ webhook ➡️ Digiflazz ➡️ Serial Number terbit ke pembeli.

---
*Dokumen ini dibuat otomatis oleh GameNexa Development Team. Jika ada pertanyaan teknis, silakan hubungi tim pengembang.*
