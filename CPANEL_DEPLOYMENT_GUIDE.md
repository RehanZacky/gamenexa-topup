# 🚀 PANDUAN LENGKAP DEPLOYMENT KE CPANEL DENGAN GIT (DOCUMENT ROOT TERKUNCI)

Panduan ini dikhususkan jika Anda ingin melakukan deploy **GameNexa Top-Up** ke hosting **cPanel** menggunakan **Git**, di mana **Document Root tidak bisa diubah** (terkunci di `public_html`).

Semua file konfigurasi (`.htaccess` root, fallback `index.php`, dan script otomatisasi `cpanel-deploy.sh`) **sudah disiapkan di repository ini** sehingga Anda cukup melakukan `git clone` atau `git pull`.

---

## 🛠️ Persiapan Awal di cPanel1

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

## ⚡ Langkah 3: Menjalankan Setup & Migration

Pilih cara yang sesuai dengan kondisi hosting Anda:

### 👉 Pilihan 1: Jika Ada Akses Terminal
Di Terminal cPanel, cukup jalankan:
```bash
bash cpanel-deploy.sh
```

---

### 👉 Pilihan 2: Jika TIDAK Ada Akses Terminal (Shared Hosting)
1. **Upload Folder `vendor`:**
   Karena hosting tanpa terminal tidak bisa menjalankan `composer install`, kompres folder `vendor` di laptop Anda menjadi `vendor.zip`, upload ke `public_html` via **File Manager cPanel**, lalu klik kanan > **Extract**.
2. **Jalankan Setup via Browser:**
   Buka browser Anda dan akses URL berikut:
   ```text
   https://domainanda.com/cpanel-setup?key=gamenexa2026
   ```
   *(Sistem akan otomatis menjalankan `storage:link`, `migrate --force`, dan `optimize:clear` langsung dari browser dengan respon status JSON!)*
3. **Alternatif via Cron Jobs:**
   Buka cPanel > menu **Cron Jobs** > Tambahkan cron job sekali jalan:
   ```text
   cd /home/username/public_html && php artisan migrate --force && php artisan storage:link && php artisan optimize:clear
   ```

---

## 🔄 Cara Update di Kemudian Hari (Jika Ada Perubahan Kode)

* **Jika ada Terminal:**
  ```bash
  cd ~/public_html
  git pull origin main
  bash cpanel-deploy.sh
  ```
* **Jika tanpa Terminal:**
  Buka cPanel > menu **Git™ Version Control** > klik **Manage** di repository Anda > klik tab **Pull or Deploy** > klik tombol **Update from Remote**. Setelah itu buka `https://domainanda.com/cpanel-setup?key=gamenexa2026` di browser.

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
