#!/usr/bin/env bash
# Menyalin kode aplikasi Toko Tumbler ke project Laravel baru.
# Pemakaian: ./install.sh /path/ke/project-laravel
set -e

TARGET="${1:?Pemakaian: ./install.sh /path/ke/project-laravel}"

if [ ! -f "$TARGET/artisan" ]; then
  echo "Error: '$TARGET' bukan folder project Laravel (file artisan tidak ditemukan)."
  exit 1
fi

SRC="$(cd "$(dirname "$0")" && pwd)"

for dir in app database resources routes tests; do
  cp -R "$SRC/$dir" "$TARGET/"
done
cp "$SRC/README.md" "$TARGET/README.md"

echo "Selesai. Langkah berikutnya (di folder project):"
echo "  1. Atur koneksi database MySQL di file .env (lihat README bagian 4)"
echo "  2. php artisan migrate:fresh --seed"
echo "  3. php artisan serve"
