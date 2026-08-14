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
`Admin | Checker Retur | Checker Terima | Checker Keluar | Checker Kiriman` — **dinamis**: bisa dibuat/diedit via UI `Pengaturan → Roles` (RoleResource).

### RBAC fine-grained (permission-based, akses nempel di user)
Authorization berbasis **permission langsung di user** (bukan role):
- **85 permission** di-seed oleh `database/seeders/PermissionSeeder.php`: 20 modul × 4 action (`view/create/update/delete_{module_key}`) + 4 widget (`view_widget_*`) + `view_all_data` (lihat semua data)
- **Role = label + `is_super_admin` (bool) + `permission_template` (json)** — template hanya untuk pre-fill checkbox saat pilih role di form user; role **TIDAK** mewariskan akses (`role_has_permissions` kosong)
- **Super Admin bypass:** `AppServiceProvider::boot()` → `Gate::before` return `true` jika `$user->isSuperAdmin()` (User model punya method `isSuperAdmin()`: punya role dengan `is_super_admin=true`)
- **UI kelola:** Create/Edit User & Role kini **modal** (halaman create/edit dihapus; form ber-`Tabs` + `columnSpanFull`). Tab "Akses Menu & Fitur" / "Detail Template" pakai matriks `App\Filament\Support\PermissionMenu` (`globalSection` Akses Global `view_all_data` → `menuSections` group collapsible, Fieldset 5 kolom Pilih Semua+Lihat/Tambah/Ubah/Hapus → `widgetsSections` Widget Aktif). Role Select `live()` → pre-fill dari `role.permission_template`. Checkbox `perm_*` dehydrated ikut `$data` → `syncPermissions()` di `CreateAction::using()`/`EditAction::using()`; load via `getDirectPermissions()`
- **RoleResource** (`Pengaturan → Roles`): CRUD role dinamis — nama, toggle super admin, permission template (hanya super admin yang bisa akses)
- **Default role:** `PermissionSeeder::setRoleDefaults()` → Admin super_admin + template=85; Checker Terima=19, Retur/Keluar=11, Kiriman=7
- **Migrasi penting:** `decouple_role_permissions_to_user` menyalin permission role → direct user lama, lalu mengosongkan `role_has_permissions`

Access pattern (dipakai di semua resource):
```php
canViewAny()            → auth()->user()->can('view_{module}')
canCreate()             → auth()->user()->can('create_{module}')
canEdit($record)        → auth()->user()->can('update_{module}')
canDelete($record)      → auth()->user()->can('delete_{module}')
shouldRegisterNavigation() → auth()->user()->can('view_{module}')
getEloquentQuery()      → where('user_id', auth()->id()) for non-Admin; if can('view_all_data') → lihat semua
```

Guard UI yang TETAP super-admin: `DeleteBulkAction` + kolom "Checker"/"Dibuat" → `isSuperAdmin()`; `StatsOverviewWidget` → generik per-modul (kartu per modul yang bisa di-view user, count all jika `view_all_data` else own).

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
| `app/Services/TableExportService.php` | Custom XLSX/PDF export (icon-only toolbar action) |
| `resources/views/exports/table-pdf.blade.php` | PDF HTML template (query-based) |
| `resources/views/exports/table-pdf-rows.blade.php` | PDF HTML template (array-based, font 7px) |
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
| `database/seeders/PermissionSeeder.php` | Seed 85 permission + default per role (template) |
| `app/Filament/Resources/Roles/` | CRUD role dinamis (RoleResource) |
| `app/Filament/Support/PermissionMenu.php` | Flat tree checkbox (dipakai UserForm & RoleForm) |

## Dependencies
- `filament/filament` — admin panel v5
- `spatie/laravel-permission` — role middleware
- Zero export/import libraries for master templates — all native ZipArchive + XML
- `dompdf/dompdf` — PDF export via `App\Services\TableExportService` (OpenSpout XLSX via `filament/actions`)

## Pull & update di PC kantor (Windows/Linux)
```bash
git pull
composer install --no-dev
php artisan migrate
php artisan optimize
npm run build
```

## graphify

This project has a knowledge graph at graphify-out/ with god nodes, community structure, and cross-file relationships.

When the user types `/graphify`, use the installed graphify skill or instructions before doing anything else.

Rules:
- For codebase questions, first run `graphify query "<question>"` when graphify-out/graph.json exists. Use `graphify path "<A>" "<B>"` for relationships and `graphify explain "<concept>"` for focused concepts. These return a scoped subgraph, usually much smaller than GRAPH_REPORT.md or raw grep output.
- Dirty graphify-out/ files are expected after hooks or incremental updates; dirty graph files are not a reason to skip graphify. Only skip graphify if the task is about stale or incorrect graph output, or the user explicitly says not to use it.
- If graphify-out/wiki/index.md exists, use it for broad navigation instead of raw source browsing.
- Read graphify-out/GRAPH_REPORT.md only for broad architecture review or when query/path/explain do not surface enough context.
- After modifying code, run `graphify update .` to keep the graph current (AST-only, no API cost).

## Workflow & Conventions

### Git & Commits
- **JANGAN PUSH KE GITHUB** setelah setiap perubahan. Push hanya saat user memberi instruksi `push` atau `push ke github`.
- Commit setelah perubahan kompleks, tapi jangan push sampai ada instruksi.
- Commit message format: `feat:` (fitur baru), `fix:` (bug fix), `refactor:` (restructure), `docs:` (dokumentasi)

### Filament Tables & Columns
- **Sortable column dengan join relationship:** gunakan `->sortable(query: function ($query, $direction) { return $query->leftJoin(...)->orderBy(...)->select('main_table.*'); })`
- **JANGAN gunakan `Expression::raw()`** di Laravel 13 — gunakan `leftJoin()` + `orderBy()` atau `DB::raw()` jika perlu raw SQL
- **TextColumn getStateUsing():** define logic untuk retrieve state dari relationship; `color()` dan `formatStateUsing()` pakai `$state` hasil dari `getStateUsing()`, bukan `$record`
- **Private method di Table class:** tidak bisa diakses dari closure `fn ($record)` — gunakan logic langsung di closure atau static method

### Features Implemented
1. **Kiriman Mobil (TaskKirimanMobil)** — Menu "Kiriman Mobil"
   - Disable `tanggal_kirim` & `branch_shipments` saat edit (read-only field)
   - `jam_tiba` required hanya saat status = 'selesai'
   - Sortable di semua kolom utama

2. **Input Kirim Barang (BranchShipment)** — Menu "Input Kirim Barang"
   - Kolom "Checker" (badge) menampilkan status check dari TaskKeluarBarang
   - Status = 'selesai' or 'siap kirim' → "Checked" (green badge)
   - Status = 'draft' or tidak ada record → "Belum di Check" (gray badge)
   - Sortable kolom "Status" & "Checker" (via leftJoin dengan task_keluar_barangs)
