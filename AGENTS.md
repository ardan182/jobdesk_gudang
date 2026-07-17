# AGENTS.md — Jobdesk Gudang AP

Laravel 13 monolith with a single Filament v5 admin panel at `/admin`. Multi-user CRUD app for warehouse daily task logging, employee leave tracking, and master data management.

## 📁 Dokumentasi — WAJIB di `.agents/`

Semua file dokumentasi (PRD, Tech Spec, Task list) HARUS disimpan di folder `.agents/`. **Jangan pernah membuat** `PRD.md`, `TASKS.md`, atau `TECH-SPEC.md` di root project — file-file itu sudah di-ignore oleh `.gitignore`.

## Setup

```bash
cp .env.example .env  # then edit DB_* for MySQL
composer install
npm install && npm run build
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve       # http://localhost:8000/admin
```

.env must use `mysql`, not the default `sqlite`:
```
DB_CONNECTION=mysql
DB_DATABASE=jobdesk_gudang
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

## Testing

```bash
composer test          # php artisan config:clear → php artisan test
```
No Pest, uses PHPUnit 12.

## Dev server

```bash
composer dev           # concurrently: artisan serve + queue:listen + pail + vite
```

## Architecture

### Panel
- Single panel at `App\Providers\Filament\AdminPanelProvider` — id `admin`, path `/admin`
- Filament v5 auto-discovery for resources, pages, widgets
- Navigation groups collapsed by default via Alpine + localStorage

### 5 roles (Spatie Permission)
`Admin | Checker Retur | Checker Terima | Checker Keluar | Checker Kiriman`

Role-based access pattern:
```php
canViewAny()        → hasRole('Admin') || hasRole('Checker X')
canDelete()         → only Admin
getEloquentQuery()  → where('user_id', auth()->id()) for non-Admin
```

### 6 task tables (one per module)
`task_retur_suppliers | task_retur_cabangs | task_datang_mobil_suppliers | task_terima_suppliers | task_keluar_barangs | task_kiriman_mobils`

All share `id_task` (indexed, not unique), `user_id` (FK). `no_baris` was dropped.

### TaskIdGenerator (`app/Services/TaskIdGenerator.php`)
Auto-generates `id_task` format `{PREFIX}-{NNNNN}` with global sequential counter via `task_id_counters` table. Prefixes: `RET-SUP`, `RET-CAB`, `ARR-SUP`, `TRM-SUP`, `KLR`, `KRM`.

### Batch insert (Repeater form)
Each module's List page has a modal with Repeater form. **Each row gets its own `id_task`** (sequential, no longer batch-shared).

### Property type quirk
Filament v5 Resource parent class uses `string|\BackedEnum|null` for `$navigationIcon` and `string|\UnitEnum|null` for `$navigationGroup`. Subclass types must match exactly.

### Export/Import (ZipArchive native)
All XLSX exports use **ZipArchive + XML manual** (no maatwebsite/phpspreadsheet). PHP 8.5+ incompatible with those packages.

## Key files

| Path | Purpose |
|------|---------|
| `app/Services/TaskIdGenerator.php` | Sequential counter ID generation |
| `app/Models/Task*` | 6 task models |
| `app/Models/WarehouseEmployee.php` | Employees + division_id |
| `app/Models/WarehouseLeave.php` | Leave/absence tracking |
| `app/Filament/Resources/` | All CRUD resources |
| `app/Filament/Pages/ManageLeaves.php` | Monthly attendance matrix |
| `app/Filament/Widgets/StatsOverviewWidget.php` | Dashboard stats |
| `app/Filament/Widgets/RecentActivityWidget.php` | Activity log |
| `app/Exports/SuppliersExport.php` | Supplier XLSX template |
| `app/Exports/EmployeesExport.php` | Employee XLSX template |
| `app/Imports/SupplierImport.php` | Supplier CSV/XLSX/XLS import |
| `app/Imports/WarehouseEmployeeImport.php` | Employee CSV/XLSX/XLS import |
| `database/seeders/RoleSeeder.php` | Seed 5 roles |

## Dependencies
- `filament/filament` — admin panel v5
- `spatie/laravel-permission` — role middleware
- Zero export/import libraries — all native ZipArchive + XML

## Pull & update di PC kantor (Windows/Linux)
```bash
git pull
composer install --no-dev
php artisan migrate
php artisan optimize
npm run build
```
