# Jobdesk Gudang AP

Aplikasi **Jobdesk Harian Gudang** berbasis web untuk digitalisasi management jobdesk harian di gudang. Dibangun dengan **Laravel 13** + **Filament v5**.

> **Live:** [gudang.mutiarasuperkitchen.com](https://gudang.mutiarasuperkitchen.com)

## Fitur

- **Role-based access** — Admin, Checker Retur, Checker Terima, Checker Keluar, Checker Kiriman
- **6 modul task** dengan form single-input modal:
  - Retur Supplier (In & Out)
  - Retur Masuk dari Toko
  - Datang Mobil Supplier
  - Terima Barang Supplier
  - Keluar Barang
  - Kiriman Mobil
- **ID_TASK** otomatis format `PREFIX-NNNNN` (global sequential counter)
- **Master data:** Ekspedisi, Kendaraan, Sopir, Toko, Supplier, Employee Gudang, Masa Berlaku STNK/KIR
- **Import/Export** Supplier & Employee (XLSX/XLS/CSV via ZipArchive)
- **Cuti & Absensi** — Monthly attendance matrix grid dengan filter
- **Dashboard** stat card real-time per role + activity log
- **TV Board** — monitoring realtime di `/tv-board` (tanpa login)
- **Aurum Theme** — tema premium Gold-on-Graphite (preset Sapphire)

## Persyaratan Sistem

- PHP 8.2+ (disarankan 8.3)
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

## Deploy ke Hosting

### Struktur Folder

```
public_html/gudang/            ← project root
public_html/gudang/public/     ← document root (web server)
```

### Langkah Deploy (Hostinger)

1. **Upload/pull** project ke `public_html/gudang/`
2. Set **document root** di cPanel/hPanel → `public_html/gudang/public/`
3. Pastikan **PHP 8.3+**
4. Buat database MySQL di hPanel, isi `.env`
5. Generate key + migrate + seed:
   ```bash
   php artisan key:generate
   php artisan migrate
   php artisan db:seed
   ```
6. Clear cache:
   ```bash
   php artisan optimize:clear
   ```

### ⚠️ Fix 403 di Production (PENTING!)

Jika login berhasil tapi semua `/admin/*` tampil **403 Forbidden**, penyebabnya adalah model `User` tidak implement `FilamentUser`. Pastikan `app/Models/User.php` berisi:

```php
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
```

### Konfigurasi Session di Hosting

Untuk shared hosting (Hostinger), gunakan session database:
```
SESSION_DRIVER=database
```
`bootstrap/app.php` sudah di-set `trustProxies` + `encryptCookies(except)` untuk session cookie.

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
| Retur Supplier | `RET-SUP` | `RET-SUP-00001` |
| Retur Cabang | `RET-CAB` | `RET-CAB-00001` |
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
| Theme | Aurum (Gold-on-Graphite, preset Sapphire) |
| Database | MySQL / MariaDB |
| Role & Permission | Spatie Laravel Permission |
| Frontend | Tailwind CSS + Alpine.js |
| Export/Import | ZipArchive (native PHP) |
