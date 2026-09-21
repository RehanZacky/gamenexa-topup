#!/bin/bash
# ==============================================================================
# GameNexa Top-Up — cPanel Post-Deployment Automation Script
# Jalankan script ini via cPanel Terminal setelah 'git clone' atau 'git pull'
# Penggunaan: bash cpanel-deploy.sh
# ==============================================================================

set -e

echo "🚀 [GameNexa] Memulai proses deployment di cPanel..."

# 1. Cek file .env
if [ ! -f ".env" ]; then
    echo "⚠️ File .env belum ditemukan! Menyalin dari .env.example..."
    cp .env.example .env
    php artisan key:generate --force
    echo "❗ PENTING: Silakan buka file .env dan masukkan konfigurasi Database, Midtrans, dan Digiflazz!"
fi

# 2. Install dependencies Composer (production)
if command -v composer &> /dev/null; then
    echo "📦 Menjalankan composer install..."
    composer install --no-dev --optimize-autoloader --no-interaction
else
    echo "ℹ️ Composer command line tidak ditemukan, melewati composer install."
fi

# 3. Jalankan Database Migration
echo "🗄️ Menjalankan database migration..."
php artisan migrate --force

# 4. Hubungkan Storage Symlink
echo "🔗 Membuat storage symlink..."
php artisan storage:link || true

# 5. Optimasi Cache Laravel Production
echo "⚡ Mengoptimalkan cache Laravel..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Set Permission Folder Wajib
echo "🔒 Memperbaiki permission folder storage dan bootstrap/cache..."
chmod -R 775 storage bootstrap/cache || chmod -R 755 storage bootstrap/cache

echo "✅ [GameNexa] Deployment selesai dengan sukses! Web siap digunakan."
