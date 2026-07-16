# Jobdesk Gudang AP

Aplikasi **Jobdesk Harian Gudang** berbasis web untuk digitalisasi management jobdesk harian di gudang. Dibangun dengan **Laravel 13** + **Filament v5**.

## Fitur

- **Role-based access** — Admin, Checker Retur, Checker Terima, Checker Keluar, Checker Kiriman
- **6 modul task** dengan form **Repeater multi-row** input cepat:
  - Retur ke Supplier
  - Retur dari Cabang
  - Datang Mobil Supplier
  - Terima Barang Supplier
  - Keluar Barang
  - Kiriman Mobil
- **ID_TASK** otomatis format `PREFIX-NNNNN` (global sequential counter)
- **6 Master data:** Ekspedisi, Kendaraan, Sopir, Toko, Supplier, Employee Gudang
- **Import/Export** Supplier & Employee (XLSX/XLS/CSV via ZipArchive)
- **Cuti & Absensi** — Monthly attendance matrix grid dengan filter
- **Dashboard** stat card real-time per role + activity log

## Persyaratan Sistem

- PHP 8.2+
- Composer 2.x
- MySQL 8.0+ / MariaDB 10.6+
- Node.js 18+ & NPM

## Instalasi

```bash
git clone https://github.com/ardan182/jobdesk_gudang.git
cd jobdesk_gudang
composer install
npm install && npm run build
cp .env.example .env
```

### Konfigurasi `.env`

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

### Lanjutan

```bash
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

Akses **http://localhost:8000/admin** — login `admin@jobdesk.test` / `password`

## Struktur Role

| Role | Akses |
|------|-------|
| **Admin** | Semua menu + CRUD user |
| **Checker Retur** | Retur Supplier, Retur Cabang |
| **Checker Terima** | Datang Mobil, Terima Barang |
| **Checker Keluar** | Keluar Barang |
| **Checker Kiriman** | Kiriman Mobil |

## Format ID_TASK

| Modul | Prefix | Contoh |
|-------|--------|--------|
| Retur ke Supplier | `RET-SUP` | `RET-SUP-00001` |
| Retur dari Cabang | `RET-CAB` | `RET-CAB-00001` |
| Datang Mobil Supplier | `ARR-SUP` | `ARR-SUP-00001` |
| Terima Barang | `TRM-SUP` | `TRM-SUP-00001` |
| Keluar Barang | `KLR` | `KLR-00001` |
| Kiriman Mobil | `KRM` | `KRM-00001` |

Global sequential counter (5 digit), per-row unique ID.

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 13 |
| Admin Panel | Filament v5 |
| Database | MySQL / MariaDB |
| Role & Permission | Spatie Laravel Permission |
| Frontend | Tailwind CSS + Alpine.js |
| Export/Import | ZipArchive (native PHP) |
