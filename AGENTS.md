# AGENTS.md — Jobdesk Gudang AP

Laravel 13 monolith with a single Filament v5 admin panel at `/admin`. Single-user CRUD app for warehouse daily task logging.

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
No Pest, uses PHPUnit 12. No test factories for task models exist yet.

## Dev server

```bash
composer dev           # concurrently: artisan serve + queue:listen + pail + vite
```

## Architecture

### Panel
- Single panel at `App\Providers\Filament\AdminPanelProvider` — id `admin`, path `/admin`
- Resources auto-discovered from `app/Filament/Resources/{ModelPlural}/` (Filament v5 per-model subdirectory layout)
- Each resource split into: `Schemas/{Model}Form.php`, `Tables/{Model}sTable.php`, `Pages/{Create|Edit|List}{Model}.php`

### 5 roles (Spatie Permission)
`Admin | Checker Retur | Checker Terima | Checker Keluar | Checker Kiriman`

Role-based access pattern (every resource follows this):
```php
canViewAny()        → auth()->user()?->hasRole('Admin') || auth()->user()?->hasRole('Checker X')
shouldRegisterNavigation() → same check
getEloquentQuery()  → where('user_id', auth()->id()) for non-Admin
```

### 5 task tables (one per module)
`task_retur_suppliers | task_retur_cabangs | task_terima_suppliers | task_keluar_barangs | task_kiriman_mobils`

All share `id_task` (indexed, not unique per migration), `no_baris` (daily counter), `user_id` (FK).

### TaskIdGenerator (`app/Services/TaskIdGenerator.php`)
Auto-generates `id_task` format `{PREFIX}-{YYYYMMDD}-{XXX}` with per-day counters. Prefixes: `RET-SUP`, `RET-CAB`, `TRM-SUP`, `KLR`, `KRM`.

### Batch insert (Repeater form)
Each module's `Create{Model}Page.php` overrides `create()` to handle multi-row input from a Filament Repeater. IDs and baris numbers are pre-calculated before the transaction to avoid counter collisions. Also have a `creating` boot event on each model as fallback for single-record creates.

### Property type quirk
Filament v5 Resource parent class uses property types `string|\BackedEnum|null` for `$navigationIcon` and `string|\UnitEnum|null` for `$navigationGroup`. Subclass types must match **exactly** — use `string|\BackedEnum|null` and `string|\UnitEnum|null`, not `?string`.

## Key files

| Path | Purpose |
|------|---------|
| `app/Services/TaskIdGenerator.php` | ID & baris counter logic |
| `app/Models/Task{ReturSupplier,ReturCabang,TerimaSupplier,KeluarBarang,KirimanMobil}.php` | 5 task models |
| `app/Filament/Resources/{Task*}/Task*Resource.php` | CRUD + role guard |
| `app/Filament/Resources/{Task*}/Pages/Create*Task*.php` | Repeater batch create |
| `app/Filament/Resources/{Task*}/Schemas/*Form.php` | Input form fields |
| `app/Filament/Resources/{Task*}/Tables/*Table.php` | Table + date filter |
| `app/Filament/Widgets/StatsOverviewWidget.php` | Dashboard cards |
| `database/seeders/RoleSeeder.php` | Seed 5 roles |

## Dependencies
- `filament/filament` — admin panel v5
- `spatie/laravel-permission` — role middleware
