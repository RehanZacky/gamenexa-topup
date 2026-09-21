# 🚀 PANDUAN LENGKAP DEPLOYMENT KE CPANEL DENGAN GIT (DOCUMENT ROOT TERKUNCI)

Panduan ini dikhususkan jika Anda ingin melakukan deploy **GameNexa Top-Up** ke hosting **cPanel** menggunakan **Git**, di mana **Document Root tidak bisa diubah** (terkunci di `public_html`).

Semua file konfigurasi (`.htaccess` root, fallback `index.php`, dan script otomatisasi `cpanel-deploy.sh`) **sudah disiapkan di repository ini** sehingga Anda cukup melakukan `git clone` atau `git pull`.

---

## 🛠️ Persiapan Awal di cPanel

### 1. Set Versi PHP
* Buka cPanel > **Select PHP Version** atau **MultiPHP Manager**.
* Pilih **PHP 8.2**, **PHP 8.3**, atau **PHP 8.4**.
* Di tab **Extensions**, pastikan ekstensi berikut aktif:
  * `pdo_mysql`, `curl`, `fileinfo`, `mbstring`, `openssl`, `xml`, `bcmath`, `zip`, `tokenizer`.

### 2. Buat Database MySQL di cPanel
1. Buka cPanel > **MySQL® Databases**.
2. Buat Database baru (contoh: `u12345_gamenexa`).
3. Buat User baru (contoh: `u12345_dbuser`) dan buat password yang kuat.
4. Hubungkan User ke Database dengan mencentang **ALL PRIVILEGES**.
5. Catat nama database, user, dan password untuk file `.env`.

---

## 🚀 Langkah 1: Clone Repository Menggunakan Git di cPanel

Anda bisa menggunakan salah satu dari dua cara berikut:

### 👉 Cara A: Menggunakan Menu "Git™ Version Control" di cPanel (Paling Mudah)
1. Buka cPanel > menu **Git™ Version Control**.
2. Klik tombol **Create**.
3. Isi formulir:
   * **Clone URL**: Masukkan URL repository GitHub Anda (contoh: `https://github.com/username/gamenexa-topup.git`).
   * **Repository Path**: Isi dengan `public_html` *(Pastikan folder public_html kosong terlebih dahulu)*.
   * **Repository Name**: `gamenexa`
4. Klik **Create**. cPanel akan meng-clone seluruh project langsung ke `public_html`.

---

### 👉 Cara B: Menggunakan Menu "Terminal" di cPanel
1. Buka cPanel > menu **Terminal**.
2. Masuk ke folder `public_html`:
   ```bash
   cd ~/public_html
   ```
3. Pastikan folder bersih dari file default (seperti `cgi-bin` atau `index.html` bawaan hosting). Jika ada, hapus:
   ```bash
   rm -rf * .htaccess
   ```
4. Clone repository langsung ke dalam folder tersebut:
   ```bash
   git clone https://github.com/username/gamenexa-topup.git .
   ```

---

## ⚙️ Langkah 2: Konfigurasi File `.env`

1. Masih di terminal (atau via **File Manager** cPanel):
   ```bash
   cp .env.example .env
   php artisan key:generate --force
   ```
2. Buka file `.env` dan sesuaikan nilainya:
   ```ini
   APP_NAME=GameNexa
   APP_ENV=production
   APP_KEY=base64:... (otomatis dibuat oleh perintah di atas)
   APP_DEBUG=false
   APP_URL=https://domainanda.com

   # Konfigurasi Database cPanel
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=u12345_gamenexa
   DB_USERNAME=u12345_dbuser
   DB_PASSWORD=password_database_anda

   # Kunci Production Digiflazz & Midtrans
   DIGIFLAZZ_USERNAME=username_anda
   DIGIFLAZZ_KEY=production_key_anda
   DIGIFLAZZ_MODE=production

   MIDTRANS_SERVER_KEY=Mid-server-xxxxxxxxxxxx
   MIDTRANS_CLIENT_KEY=Mid-client-xxxxxxxxxxxx
   MIDTRANS_IS_PRODUCTION=true
   ```

---

## ⚡ Langkah 3: Jalankan Script Otomatisasi (1 Perintah Selesai)

Di Terminal cPanel, cukup jalankan:
```bash
bash cpanel-deploy.sh
```

Script ini akan otomatis menjalankan:
1. `composer install --no-dev --optimize-autoloader`
2. `php artisan migrate --force`
3. `php artisan storage:link`
4. `php artisan optimize:clear`
5. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
6. `chmod -R 775 storage bootstrap/cache`

---

## 🔄 Cara Update di Kemudian Hari (Jika Ada Perubahan Kode)

Kapan pun Anda melakukan update di lokal dan push ke GitHub, di server cPanel Anda cukup membuka **Terminal** dan menjalankan:

```bash
cd ~/public_html
git pull origin main
bash cpanel-deploy.sh
```

Website akan langsung ter-update dengan aman!

---

## ⚠️ Checklist Wajib untuk Top-Up Game (Penting):

1. **Whitelist IP Hosting di Digiflazz:**
   * Di dashboard utama cPanel, lihat sidebar kanan: **Shared IP Address** (atau *Server IP*).
   * Masuk ke dashboard **Digiflazz** > **Whitelist IP** > Daftarkan IP hosting tersebut.
2. **Pasang Webhook URL:**
   * **Midtrans:** `https://domainanda.com/webhook/midtrans`
   * **Digiflazz:** `https://domainanda.com/webhook/digiflazz`
3. **Pastikan SSL (HTTPS) Aktif:**
   * Buka menu **SSL/TLS Status** di cPanel > klik **Run AutoSSL** agar domain memiliki sertifikat SSL valid.
