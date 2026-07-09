# Jobdesk Gudang AP

Aplikasi **Jobdesk Harian Gudang** berbasis web untuk mendigitalisasi management jobdesk harian di gudang. Dibangun dengan **Laravel 13** + **Filament v5**.

## Fitur

- **Role-based access** — Admin, Checker Retur, Checker Terima, Checker Keluar, Checker Kiriman
- **5 modul task** dengan form **Repeater multi-row** input cepat:
  - Retur ke Supplier (Servis/Tukar/Pot Nota)
  - Terima Retur dari Cabang (Retur Jelek/Retur Bagus)
  - Terima Barang dari Supplier (Komplit/Kurang/Lebih)
  - Keluar Barang ke Toko/Cabang
  - Kiriman Cabang Per Mobil (jam muat, selesai, berangkat)
- **ID_TASK & NO_BARIS** otomatis (format: `PREFIX-YYYYMMDD-XXX`)
- **Dashboard** stat card real-time per role
- **Laporan** dengan filter tanggal, search, pagination, sort

## Persyaratan Sistem

- PHP 8.2+
- Composer 2.x
- MySQL 8.0+ / MariaDB 10.6+
- Node.js 18+ & NPM (untuk Filament assets)

## Instalasi

```bash
# 1. Clone repository
git clone https://github.com/ardan182/jobdesk_gudang.git
cd jobdesk_gudang

# 2. Install dependencies PHP
composer install

# 3. Install dependencies frontend (Filament assets)
npm install
npm run build

# 4. Copy environment file
cp .env.example .env
```

### Konfigurasi `.env`

Edit file `.env` dan sesuaikan konfigurasi database:

```env
APP_NAME=JobdeskGudangAP
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jobdesk_gudang
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### Lanjutan Instalasi

```bash
# 5. Generate application key
php artisan key:generate

# 6. Buat database
# Buat database "jobdesk_gudang" di MySQL/MariaDB

# 7. Jalankan migrasi
php artisan migrate

# 8. Seed role, user admin, & data awal
php artisan db:seed
# User admin@jobdesk.test / password otomatis dibuat dengan role Admin

# 9. Jalankan development server
php artisan serve
```

## Login

Akses **http://localhost:8000/admin**

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@jobdesk.test` | `password` |

Setelah login sebagai Admin, buat user untuk role lain di menu **Users**.

## Struktur Role

| Role | Akses |
|------|-------|
| **Admin** | Semua menu + CRUD user |
| **Checker Retur** | Dashboard, Retur ke Supplier, Retur dari Cabang |
| **Checker Terima** | Dashboard, Terima Barang Supplier |
| **Checker Keluar** | Dashboard, Keluar Barang |
| **Checker Kiriman** | Dashboard, Kiriman Cabang Per Mobil |

## Format ID_TASK

| Modul | Prefix | Contoh |
|-------|--------|--------|
| Retur ke Supplier | `RET-SUP` | `RET-SUP-20260709-001` |
| Retur dari Cabang | `RET-CAB` | `RET-CAB-20260709-001` |
| Terima Barang | `TRM-SUP` | `TRM-SUP-20260709-001` |
| Keluar Barang | `KLR` | `KLR-20260709-001` |
| Kiriman Mobil | `KRM` | `KRM-20260709-001` |

Counter di-reset setiap hari.

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 13 |
| Admin Panel | Filament v5 |
| Database | MySQL / MariaDB |
| Role & Permission | Spatie Laravel Permission |
| Frontend | Tailwind CSS (bundled Filament) |
