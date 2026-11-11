# 🔥 Troubleshooting Error 500 di Server Hosting

## Cara Cepat Menggunakan Helper File

1. **Upload file `public/fix-500.php` ke server**
2. **Akses via browser**: `https://yourdomain.com/fix-500.php?key=gamenexa2026`
3. **Lihat hasil diagnostic dan ikuti instruksi**
4. **HAPUS file tersebut setelah selesai!**

---

## Manual Troubleshooting (via cPanel atau SSH)

### ✅ Checklist Utama:

#### 1. **Cek File .env**
```bash
# Pastikan file .env ada di root project
# Jika tidak ada, copy dari .env.example
cp .env.example .env

# Edit dan sesuaikan konfigurasi:
nano .env
```

**Setting penting di .env:**
```env
APP_NAME=GameNexa
APP_ENV=production
APP_KEY=base64:xxxxxxxxxxxxx  # HARUS DIISI!
APP_DEBUG=false  # Set true untuk melihat error detail
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=user_database_anda
DB_PASSWORD=password_database_anda
```

#### 2. **Generate APP_KEY (jika belum ada)**
```bash
php artisan key:generate
```

#### 3. **Set Permission Folder (PENTING!)**
```bash
# Via SSH
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Atau via File Manager cPanel:
# Klik kanan folder → Change Permissions → Set to 775
```

#### 4. **Clear All Cache**
```bash
php artisan optimize:clear
# atau
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

#### 5. **Setup Storage Link**
```bash
php artisan storage:link
```

#### 6. **Install/Update Dependencies**
```bash
composer install --optimize-autoloader --no-dev
```

---

## Cara Troubleshooting via cPanel (Tanpa SSH)

### A. Gunakan cPanel Setup Helper
Akses: `https://yourdomain.com/cpanel-setup?key=gamenexa2026`

### B. Manual via File Manager cPanel

1. **Cek .env file:**
   - Buka File Manager
   - Pastikan file `.env` ada di root
   - Klik kanan → Edit
   - Pastikan `APP_KEY=base64:...` sudah terisi

2. **Fix Permissions:**
   - Klik kanan folder `storage` → Change Permissions → 775
   - Klik kanan folder `bootstrap/cache` → Change Permissions → 775
   - Centang "Recurse into subdirectories"

3. **Delete Cache Files:**
   - Hapus semua file di `bootstrap/cache/` (KECUALI .gitignore)
   - File yang dihapus: config.php, routes.php, services.php, packages.php

4. **Cek Log Error:**
   - Buka `storage/logs/laravel.log`
   - Lihat error terakhir untuk tahu penyebab spesifik

---

## Error 500 Penyebab Umum & Solusi

### ❌ Error: "No application encryption key has been specified"
**Solusi:**
```bash
php artisan key:generate
```

### ❌ Error: "Permission denied"
**Solusi:**
```bash
chmod -R 775 storage bootstrap/cache
```

### ❌ Error: "Class not found" atau "Autoload error"
**Solusi:**
```bash
composer dump-autoload
# atau
composer install --optimize-autoloader
```

### ❌ Error: "SQLSTATE connection refused"
**Solusi:**
- Cek koneksi database di .env
- Pastikan DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD benar

### ❌ Error: "File not found: bootstrap/app.php"
**Solusi:**
- Pastikan upload semua file Laravel termasuk folder bootstrap/

---

## Cara Melihat Error Detail

### Method 1: Enable Debug Mode
Edit `.env`:
```env
APP_DEBUG=true
APP_ENV=local
```
⚠️ **JANGAN LUPA** set kembali ke `false` setelah selesai!

### Method 2: Cek Laravel Log
Buka file: `storage/logs/laravel.log`

### Method 3: Cek Error Log cPanel
Di cPanel → Metrics → Errors

---

## Struktur Folder yang Benar

```
your-domain.com/
├── .env                    ← HARUS ADA!
├── .htaccess              ← Redirect ke public/
├── public/
│   ├── index.php          ← Entry point
│   ├── .htaccess
│   └── storage/           ← Symlink (dibuat via storage:link)
├── storage/               ← HARUS WRITABLE (775)
│   ├── app/
│   ├── framework/
│   └── logs/
├── bootstrap/
│   └── cache/             ← HARUS WRITABLE (775)
└── vendor/                ← Hasil composer install
```

---

## Kontak Support

Jika masih error setelah semua langkah:
1. Screenshot error message
2. Copy isi `storage/logs/laravel.log` (10 baris terakhir)
3. Hubungi: support@gamenexa.com

---

**Dibuat: Oktober 2026**
**Update terakhir: 16 Oktober 2026**
