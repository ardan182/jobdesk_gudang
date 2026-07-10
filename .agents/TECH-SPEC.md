# TECH SPEC: Jobdesk Gudang AP

## BAGIAN 1: Tech Stack & Arsitektur

### Tech Stack

| Layer | Technology | Version |
|-------|------------|---------|
| Backend | Laravel | 13.x |
| Language | PHP | 8.3+ |
| Admin Panel | Filament | v5.x |
| Theme | Filament Nord Theme (fork `ardan182/filament-nord-theme`) | 3.x-dev |
| Frontend Rendering | Livewire (bundled with Filament) | 3.x |
| Styling | Tailwind CSS (bundled with Filament) | 4.x |
| Database | MySQL / MariaDB | 8.0+ / 10.6+ |
| ORM | Eloquent (Laravel built-in) | — |
| Auth | Filament Auth (session-based) | — |
| Roles & Permissions | Spatie Laravel Permission | — |
| Hosting | Local (develop), VPS/Shared (production) | — |

### Arsitektur Sistem

```
User (Browser) → Filament Panel (Laravel + Livewire) → Eloquent ORM → MySQL
                        │
                   ┌─────┴─────┐
                   │  Storage   │
                   │ (session,  │
                   │  cache)    │
                   └───────────┘
```

Monolith architecture: Filament Panel sebagai single entry point. Semua logic di Laravel Controllers / Filament Resources / Actions. Tidak ada API terpisah (V1 tidak perlu SPA).

### Struktur Folder

```
jobdeskgudang/
├── app/
│   ├── Filament/
│   │   ├── Resources/
│   │   │   ├── UserResource.php
│   │   │   ├── TaskReturSupplierResource.php
│   │   │   ├── TaskReturCabangResource.php
│   │   │   ├── TaskTerimaSupplierResource.php
│   │   │   ├── TaskKeluarBarangResource.php
│   │   │   └── TaskKirimanMobilResource.php
│   │   ├── Pages/
│   │   │   └── Dashboard.php
│   │   └── Widgets/
│   │       └── StatsOverviewWidget.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── TaskReturSupplier.php
│   │   ├── TaskReturCabang.php
│   │   ├── TaskTerimaSupplier.php
│   │   ├── TaskKeluarBarang.php
│   │   └── TaskKirimanMobil.php
│   ├── Services/
│   │   └── TaskIdGenerator.php
│   └── Providers/
│       └── AppServiceProvider.php
├── database/
│   ├── migrations/
│   │   ├── xxxx_create_task_retur_suppliers_table.php
│   │   ├── xxxx_create_task_retur_cabangs_table.php
│   │   ├── xxxx_create_task_terima_suppliers_table.php
│   │   ├── xxxx_create_task_keluar_barangs_table.php
│   │   └── xxxx_create_task_kiriman_mobils_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── config/
│   ├── filament.php
│   └── permission.php
├── routes/
│   └── web.php
└── resources/
    └── views/
```

### Justifikasi
- **Laravel 11:** Matur, ekosistem luas, Filament native support, ORM Eloquent mempermudah development cepat.
- **Filament v3:** Dashboard, CRUD, form repeater, table grid — semua fitur PRD sudah built-in. Bisa generate resources cepat.
- **MySQL:** Relasional, cocok untuk data task dengan struktur tetap, familiar di shared hosting.
- **Spatie Permission:** Role management fleksibel, integrasi mulus dengan Filament.

---

## BAGIAN 2: Database Design

### Ringkasan Database

| Item | Detail |
|------|--------|
| Database | MySQL 8+ / MariaDB 10.6+ |
| ORM | Eloquent (Laravel) |
| Pendekatan | Relational |
| Tools Migrasi | Laravel Migrations |
| Engine | InnoDB |

### Entity Overview

#### 1. `users` (Laravel default + role)

| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | PK |
| name | string(255) | |
| email | string(255) | unique |
| password | string(255) | hashed (bcrypt) |
| email_verified_at | timestamp | nullable |

Role disimpan via Spatie Permission (`model_has_roles` pivot).

#### 2. `task_retur_suppliers` — Kirim Retur ke Supplier

| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | PK |
| id_task | string(20) | unique, format: `RET-SUP-YYYYMMDD-XXX` |
| no_baris | integer | auto increment per hari |
| nama_supplier_ekspedisi | string(255) | |
| no_plat_mobil | string(20) | |
| nama_sopir | string(255) | |
| jam_muat | time | time picker |
| jumlah_kolian | integer | |
| admin_sj_retur | string(255) | |
| status | enum | `servis`, `tukar`, `pot_nota` |
| keterangan | text | nullable |
| user_id | foreignId | → users.id |
| created_at | timestamp | |
| updated_at | timestamp | |

**Index:** `id_task` (unique), `user_id` (FK), `created_at`

#### 3. `task_retur_cabangs` — Terima Retur dari Cabang

| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | PK |
| id_task | string(20) | unique, format: `RET-CAB-YYYYMMDD-XXX` |
| no_baris | integer | auto increment per hari |
| cabang | string(255) | |
| jenis_retur | enum | `retur_jelek`, `retur_bagus` |
| no_sj_retur | string(100) | |
| total_kolian | integer | |
| jam_bongkar | time | |
| nama_sopir | string(255) | |
| keterangan | text | nullable |
| user_id | foreignId | → users.id |
| created_at | timestamp | |
| updated_at | timestamp | |

**Index:** `id_task` (unique), `user_id` (FK), `created_at`

#### 4. `task_terima_suppliers` — Terima Barang dari Supplier

| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | PK |
| id_task | string(20) | unique, format: `TRM-SUP-YYYYMMDD-XXX` |
| no_baris | integer | auto increment per hari |
| nama_supplier_ekspedisi | string(255) | |
| no_po_referensi | string(100) | |
| jumlah_kolian | integer | |
| jam_bongkar | time | |
| nama_sopir | string(255) | |
| status | enum | `komplit`, `kurang`, `lebih` |
| keterangan | text | nullable |
| user_id | foreignId | → users.id |
| created_at | timestamp | |
| updated_at | timestamp | |

**Index:** `id_task` (unique), `user_id` (FK), `created_at`

#### 5. `task_keluar_barangs` — Keluar Barang dari Gudang

| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | PK |
| id_task | string(20) | unique, format: `KLR-YYYYMMDD-XXX` |
| no_baris | integer | auto increment per hari |
| toko_tujuan | enum | `pusat`, `ujungberung`, `soreang`, `majalaya`, `cicaheum`, `barokah` |
| supplier | string(255) | |
| no_referensi_sj | string(100) | |
| jumlah_kolian | integer | |
| jam_naik | time | |
| nama_koordinator | string(255) | |
| status | enum | `komplit`, `kurang`, `lebih` |
| keterangan | text | nullable |
| user_id | foreignId | → users.id |
| created_at | timestamp | |
| updated_at | timestamp | |

**Index:** `id_task` (unique), `user_id` (FK), `created_at`

#### 6. `task_kiriman_mobils` — Kiriman Cabang Per Mobil

| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | PK |
| id_task | string(20) | unique, format: `KRM-YYYYMMDD-XXX` |
| no_baris | integer | auto increment per hari |
| cabang | string(255) | |
| no_plat_mobil | string(20) | |
| jam_muat | time | |
| jam_selesai_muat | time | |
| jam_berangkat | time | |
| nama_supir | string(255) | |
| keterangan | text | nullable |
| user_id | foreignId | → users.id |
| created_at | timestamp | |
| updated_at | timestamp | |

**Index:** `id_task` (unique), `user_id` (FK), `created_at`

### Entity Relationship Diagram

```
users
  │
  ├──< task_retur_suppliers  (user_id)
  ├──< task_retur_cabangs    (user_id)
  ├──< task_terima_suppliers (user_id)
  ├──< task_keluar_barangs   (user_id)
  └──< task_kiriman_mobils   (user_id)
```

### ID_TASK Format & Generator

Format: `{PREFIX}-{YYYYMMDD}-{XXX}`

| Tabel | Prefix |
|-------|--------|
| task_retur_suppliers | RET-SUP |
| task_retur_cabangs | RET-CAB |
| task_terima_suppliers | TRM-SUP |
| task_keluar_barangs | KLR |
| task_kiriman_mobils | KRM |

`{XXX}` = auto-increment 3 digit per hari per tipe task (reset setiap hari).

### NO BARIS

Auto-increment integer per hari per tipe task (reset setiap jam 00:00).

---

## BAGIAN 3: Interface Design

Karena menggunakan **Laravel + Filament (monolith)**, semua interface adalah Filament Resources & Pages.

### Panel: Admin Panel (Single Panel)

| Filament Resource | Description | Akses Role |
|------------------|-------------|------------|
| UserResource | CRUD user + role | Admin |
| TaskReturSupplierResource | Input & laporan retur ke supplier | Admin, Checker Retur |
| TaskReturCabangResource | Input & laporan terima retur cabang | Admin, Checker Retur |
| TaskTerimaSupplierResource | Input & laporan terima barang supplier | Admin, Checker Terima |
| TaskKeluarBarangResource | Input & laporan keluar barang | Admin, Checker Keluar |
| TaskKirimanMobilResource | Input & laporan kiriman mobil | Admin, Checker Kiriman |

### Navigation Structure

```
Dashboard (semua role)
├── Checker Retur          ┐
│   ├── Retur ke Supplier  │── hanya muncul jika role = Checker Retur
│   └── Retur dari Cabang  ┘
├── Terima Barang          ── hanya muncul jika role = Checker Terima
├── Keluar Barang          ── hanya muncul jika role = Checker Keluar
├── Kiriman Mobil          ── hanya muncul jika role = Checker Kiriman
└── Users                  ── hanya Admin
```

### Dashboard Widgets (card stats)
- **Admin:** Total task per checker hari ini (5 card: ReturSupplier, ReturCabang, TerimaSupplier, KeluarBarang, KirimanMobil)
- **Checker:** Total task miliknya hari ini (1 card) + breakdown status

### Form Input (Repeater)
Setiap resource input menggunakan **Filament Repeater** untuk multi-row input:
- ID_TASK & NO BARIS: otomatis (hidden/disabled), di-generate backend
- Field lain: sesuai spesifikasi per tipe task
- Time picker: Filament TimePicker
- Dropdown: Filament Select

### Laporan (Table)
Setiap resource juga sebagai laporan dengan:
- **Pagination** (bawaan Filament)
- **Sortable columns** (bawaan Filament)
- **Filter tanggal** (Filament DatePicker filter)
- **Search** (Filament global search / table search)

---

## BAGIAN 4: Alur Logika & Business Rules

### Alur Login & Role

```
User buka app → Filament Login
  ↓
Validasi email + password
  ↓
Cek role user (Spatie Permission)
  ↓
Redirect ke Dashboard
  ↓
Sidebar navigasi menyesuaikan role
  (Checker hanya lihat resource sesuai role-nya)
```

### Alur Input Task (Repeater Form)

```
Checker buka halaman resource (misal: Retur ke Supplier)
  ↓
Tampil form repeater (kosong, siap diisi)
  ↓
User klik "Add" → baris baru muncul
  ↓
ID_TASK & NO BARIS: generated otomatis via Ajax/Livewire saat render baris
  - ID_TASK: RET-SUP-20260101-001
  - NO BARIS: 1
  ↓
User isi field (dropdown, time picker, text)
  ↓
User klik "Simpan"
  ↓
Validasi server-side (required fields, format)
  ↓
INSERT semua baris ke database
  ↓
Dashboard card update (Livewire refresh)
  ↓
Success notification
```

### Alur Laporan

```
User buka resource (list table)
  ↓
Default: tampilkan data hari ini
  ↓
User bisa:
  - Filter → pilih rentang tanggal
  - Sort → klik header kolom
  - Search → ketik keyword
  ↓
Filament built-in Table component handle semua
```

### Business Rules

1. **ID_TASK:** Format `{PREFIX}-{YYYYMMDD}-{XXX}`. Counter `{XXX}` di-reset setiap hari. Dibangkitkan otomatis oleh `TaskIdGeneratorService`.
2. **NO BARIS:** Auto increment integer per hari per tipe task. Reset ke 1 setiap hari.
3. **Role-based access:** Checker hanya bisa lihat & input resource sesuai role-nya. Admin bisa lihat semua.
4. **Status default:** Belum diisi saat create (opsional, tergantung flow).
5. **User hanya bisa lihat task milik sendiri** (kecuali Admin yang lihat semua). Diimplementasikan via Filament `Table` query scope.
6. **Repeater:** Semua baris dalam satu form di-save dalam satu transaksi database.

---

## BAGIAN 5: Keamanan, Performa, & Deployment

### Keamanan
- **Autentikasi:** Filament built-in auth (session-based)
- **Role & Permission:** Spatie Laravel Permission
  - Guard setiap resource dengan `can()` atau `role()` middleware
  - Checker hanya bisa akses resource sesuai role
  - Admin punya akses universal
- **Password:** bcrypt (default Laravel)
- **Session:** Encrypted, HTTP-only cookies
- **CSRF:** Laravel built-in CSRF protection

### Performa
- **Filament Caching:** Cache konfigurasi Filament (`php artisan filament:cache`)
- **Query Optimization:** Filter & search menggunakan index database (created_at, user_id, id_task)
- **Scope default:** Setiap resource hanya query data checker sendiri (`where('user_id', auth()->id())`) — Admin tanpa scope
- **Pagination:** Filament default pagination (10-25 per halaman)

### Deployment
- **Development:** `php artisan serve` (lokal)
- **Production:** VPS / shared hosting dengan:
  - Nginx / Apache
  - PHP 8.2+
  - MySQL 8+
  - Composer autoload optimization
  - Environment variables (.env)
- **Deployment steps:**
  1. `composer install --optimize-autoloader --no-dev`
  2. `php artisan migrate`
  3. `php artisan db:seed` (untuk role & admin user)
  4. `php artisan config:cache`
  5. `php artisan route:cache`
  6. `php artisan view:cache`
  7. `php artisan storage:link`

### Development Setup

```bash
# Clone project
git clone <repo-url> jobdeskgudang
cd jobdeskgudang

# Install dependencies
composer install
npm install

# Environment
cp .env.example .env
# edit .env: DB_DATABASE, DB_USERNAME, DB_PASSWORD

# Generate key
php artisan key:generate

# Migrate & seed
php artisan migrate
php artisan db:seed

# Storage link
php artisan storage:link

# Run dev
php artisan serve
npm run dev
```

### Environment Checklist

```
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
```

---

## ✅ TECH SPEC SELESAI! 🎉

Simpan file ini sebagai `.agents/TECH-SPEC.md`

**Lanjut ke Task Generator?** Ketik: `"Buat Task dari Tech Spec ini"` untuk breakdown pekerjaan implementasi 🚀
