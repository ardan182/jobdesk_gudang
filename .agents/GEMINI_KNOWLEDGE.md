# Project Context: Jobdesk Gudang AP

**Versi:** 2.5 | **Tanggal:** 9 Agustus 2026

---

## 1. Deskripsi

Aplikasi web untuk digitalisasi jobdesk harian gudang — pencatatan retur, penerimaan barang, pengiriman barang, cuti karyawan, dan master data. Dibangun dengan Laravel 13 monolith + Filament v5 admin panel.

**Akses:** `http://localhost:8000/admin`  
**Login:** `admin@jobdesk.test` / `password`

---

## 2. Tech Stack

| Layer | Teknologi | Catatan |
|-------|-----------|---------|
| Backend | Laravel 13 | PHP 8.5.8 |
| Admin Panel | Filament v5 | Auto-discover resources, pages, widgets |
| Database | MySQL / MariaDB | Wajib `mysql`, bukan sqlite |
| Auth | Spatie Laravel Permission | 5 role dinamis + 85 permission (RBAC fine-grained) |
| Frontend | Tailwind CSS + Alpine.js | Bundled via Filament |
| Assets | Vite | `npm run dev` atau `npm run build` |
| Export/Import | ZipArchive (native PHP) | Template master + import; Export table via TableExportService |
| Code Graph | Graphify | `graphify-out/` |

---

## 3. Setup & Deployment

### Development
```bash
cp .env.example .env   # edit DB_* for mysql
composer install
npm install && npm run build
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve       # http://localhost:8000/admin
composer dev            # concurrently: serve + queue:listen + pail + vite
```

### Testing
```bash
composer test   # php artisan config:clear && php artisan test
```
PHPUnit 12 (no Pest).

### Pull di PC Kantor
```bash
git pull
composer install --no-dev
php artisan migrate
php artisan optimize
npm run build
```

### Deploy di Hostinger (gudang.mutiarasuperkitchen.com)
- Document root: `public_html/gudang/public/`
- PHP 8.3+, MySQL via hPanel
- `SESSION_DRIVER=database` untuk session
- `bootstrap/app.php` sudah di-set `trustProxies` + `encryptCookies(except)` untuk session cookie

### ⚠️ KRITIS: User model WAJIB implement FilamentUser
Di production, Filament `Authenticate` middleware memblokir panel (403) jika User model tidak implement `FilamentUser`. Sudah di-fix di `app/Models/User.php`:
```php
class User extends Authenticatable implements FilamentUser
{
    public function canAccessPanel(Panel $panel): bool { return true; }
}
```
Jangan hapus `implements FilamentUser` ini saat mengubah model User!

---

## 4. Navigation Groups

| Group | Menu | Ikon | Role Access (default) | Filter Layout |
|-------|------|------|-------------|---------------|
| **Dashboard** (no group) | Dasbor | home | Semua role | — |
| **Master** | Master Ekspedisi | BuildingOffice2 | Admin | Dropdown |
| | Master Kendaraan | Truck | Admin | Dropdown |
| | Master Sopir | Users | Admin | Dropdown |
| | Master Toko | BuildingStorefront | Admin | Dropdown |
| | Master Supplier | BuildingStorefront | Admin | Dropdown |
| | Master Employee Gudang | UserGroup | Admin | Dropdown |
| **Purchasing Order** | Komplain PO | DocumentText | Admin, Checker Terima | — |
| **Retur** | Retur Masuk dari Toko | ArrowUturnLeft | Admin, Checker Retur | **AboveContent** (4 col) |
| | Retur In & Out Supplier | ArrowUturnLeft | Admin, Checker Retur | Dropdown |
| **Penerimaan** | Input SJ dari Supplier | DocumentText | Admin, Checker Terima | **AboveContent** (3 col) |
| | Datang Mobil Supplier | Truck | Admin, Checker Terima | **AboveContent** (5 col) |
| | Checker Terima Barang Supplier | ClipboardDocumentList | Admin, Checker Terima | **AboveContent** (4 col) |
| **Pengiriman** | Input Kirim Barang | PaperAirplane | Admin, Checker Keluar | **AboveContent** (5 col) |
| | Checker Keluar Barang | ClipboardDocumentCheck | Admin, Checker Keluar | **AboveContent** (3 col) |
| | Kiriman Mobil | Truck | Admin, Checker Kiriman | **AboveContent** (4 col) |
| **Administrasi** | Cuti & Absensi | CalendarDays | Admin | — |
| | Pusat Dokumen | DocumentArrowDown | Admin (CRUD), all (view) | Dropdown |
| **Pengaturan** | Users | RectangleStack | Admin | Dropdown |
| | Pengaturan Board TV | tv | Admin | — |

> Kolom "Role Access" = **default** role. Semua menu kini di-gate **permission RBAC** (`{action}_{module_key}`, 84 total) — Admin bisa kustomisasi per-user via Edit User → Section "Akses Menu & Fitur". Admin selalu full access (bypass).

---

## 5. Roles & Access

### 5.1 Default Role-Based Access

| Role | Hak Akses |
|------|-----------|
| **Admin** | Full — semua menu, semua data semua user, CRUD user, delete all records (super admin bypass) |
| **Checker Retur** | Retur Masuk Cabang, Retur Keluar Supplier — hanya data sendiri, tidak bisa delete |
| **Checker Terima** | Datang Mobil, Terima Barang Supplier, Input SJ, Komplain PO — hanya data sendiri |
| **Checker Keluar** | Keluar Barang, Input Kirim Barang — hanya data sendiri |
| **Checker Kiriman** | Kiriman Mobil — hanya data sendiri |

### 5.2 Fine-Grained Permission (Akses Nempel di User)

**Akses = direct permission user.** Role dinamis (CRUD via `Pengaturan → Roles`) = label + `is_super_admin` + `permission_template` (pre-fill saat pilih role di form user); role TIDAK mewariskan akses (`role_has_permissions` kosong).

- Format permission: `{action}_{module_key}` — view/create/update/delete
- **85 permission total** (20 modul × 4 + 4 widget `view_widget_*` + `view_all_data`), di-seed `database/seeders/PermissionSeeder.php`
- Semua modul: task, master, non-task (Input SJ, BranchShipment, Retur, Pusat Dokumen, Cuti & Absensi, Users, TvBoard, KendaraanDokumen)
- UI akses di **modal User & Role** (tab "Akses Menu & Fitur" / "Detail Template") — shared `App\Filament\Support\PermissionMenu`:
  - `globalSection()` Akses Global → `menuSections()` group collapsible + modul `Fieldset` 5 kolom (Pilih Semua|Lihat|Tambah|Ubah|Hapus) dalam `Grid(2)` → `widgetsSections()` widget Aktif
  - Form dibungkus `Tabs::make()->columnSpanFull()` (anti kolom miring)
- State field `perm_*` (dehydrated) → `syncPermissions()`; sync di **`CreateAction::using()` / `EditAction::using()`** (modal, bukan afterCreate/afterSave); load via `getDirectPermissions()`
- **Super Admin bypass:** `Gate::before` return `true` jika `$user->isSuperAdmin()` (punya role `is_super_admin=true`)
- **`view_all_data`** → lihat semua data lintas modul (scoping `getEloquentQuery` + statistik dashboard)

### Pattern Authorization
```php
canViewAny()     → auth()->user()->can('view_{module}')
canCreate()      → auth()->user()->can('create_{module}')
canEdit($record) → auth()->user()->can('update_{module}')
canDelete($record)→ auth()->user()->can('delete_{module}')
getEloquentQuery() → filter own data jika non-Admin; `can('view_all_data')` → semua
```
Diterapkan di 18 resource + 2 custom page (ManageLeaves `view_cuti_absensi`, ManageTvBoard `view_board_tv_settings`) + 4 widget (`view_widget_*`). Guard UI (bulk-delete, kolom Checker) → `isSuperAdmin()`; dashboard stats generik per-modul.

---

## 6. Database Schema

### Task Tables (6) — Log Harian

**Task Retur Supplier** (`task_retur_suppliers`)
`id_task, nama_supplier_ekspedisi, no_plat_mobil, nama_sopir, jam_muat, jumlah_kolian, admin_sj_retur, status (servis/tukar/pot_nota), keterangan, arrival_supplier_truck_id (FK), user_id`

**Task Retur Cabang** (`task_retur_cabangs`)
`id_task, cabang, jenis_retur (retur_jelek/retur_bagus), no_sj_retur, total_kolian, jam_bongkar, nama_sopir, keterangan, user_id`

**Task Terima Supplier** (`task_terima_suppliers`)
`id_task, arrival_supplier_truck_id (FK), nama_supplier_ekspedisi, no_po_referensi (nullable), jam_datang, jumlah_kolian, jam_bongkar, selesai_bongkar (nullable), lembar_sj, nama_sopir, status (DRAFT/SELESAI), keterangan, user_id`

**Task Keluar Barang** (`task_keluar_barangs`)
`id_task, branch_shipment_id (FK, nullable), cabang, nomor_sj, total_qty, no_po, jam_disiapkan (nullable), diserahkan_kepada (nullable), helper (JSON, nullable), status (draft/siap kirim/selesai), keterangan (nullable), user_id`
> Kolom lama (toko_tujuan, supplier, no_referensi_sj, jumlah_kolian, jam_naik, nama_koordinator) sudah dihapus.

**Task Kiriman Mobil** (`task_kiriman_mobils`)
`id_task, cabang, no_plat_mobil (nullable), jam_muat (nullable), jam_selesai_muat (nullable), jam_berangkat (nullable), jam_tiba (nullable), tanggal_kirim (nullable), nama_supir (nullable), status (draft/dalam pengiriman/selesai), retur_option (tidak_ada_retur/ada_retur) (nullable), keterangan (nullable), keluar_barang_id (FK, nullable), user_id`

**Arrival Supplier Trucks** (`arrival_supplier_trucks`)
`id_task, supplier_id (FK), expedition_id (FK, nullable), nama_sopir, no_plat_mobil, jenis_kiriman (DATANG/RETUR/DATANG & RETUR), tanggal_datang, jam_datang, jam_selesai (nullable), status (MENGANTRI/PROSES/SELESAI), keterangan (nullable), user_id`

### Master Tables (7)
Same as before — expeditions, master_kendaraans, master_sopirs, master_tokos, suppliers, warehouse_employees, divisions

### Non-Task Tables (6)
`supplier_sjs, branch_shipments, supplier_return_inbounds, branch_return_outbounds, warehouse_leaves, activity_logs`

### Support Tables
`task_terima_supplier_helpers` (pivot)
`task_id_counters` (global counter)
`branch_shipment_kiriman_mobil` (pivot: task_kiriman_mobils ↔ branch_shipments)
`warehouse_documents` (Pusat Dokumen)

### Aturan KIR (Masa Berlaku STNK/KIR)
- KIR hanya tampil jika `masa_berlaku_kir` terisi (grid Masa Berlaku: `where jenis != kir OR masa_berlaku != null`)
- **Motor** → tanpa KIR: field `no_kir`/`masa_berlaku_kir` tersembunyi di form (visible by `jenis_kendaraan !== 'motor'`), tidak auto-create/sync KIR di model
- **Mobil pribadi** (kosong) → KIR tersembunyi; **mobil isi masa_berlaku_kir** → KIR muncul
- Grid Master Kendaraan: kolom KIR `->visible(fn ($record) => filled($record?->masa_berlaku_kir))`

---

## 7. ID_TASK Generation

**Service:** `app/Services/TaskIdGenerator.php`

Format: `{PREFIX}-{NNNNN}` (5 digit global sequential counter)

| Modul | Prefix | Tabel |
|-------|--------|-------|
| Retur Supplier | `RET-SUP` | `task_retur_suppliers` |
| Retur Cabang | `RET-CAB` | `task_retur_cabangs` |
| Terima Supplier | `TRM-SUP` | `task_terima_suppliers` |
| Keluar Barang | `KLR` | `task_keluar_barangs` |
| Kiriman Mobil | `KRM` | `task_kiriman_mobils` |
| Datang Mobil Supplier | `ARR-SUP` | `arrival_supplier_trucks` |
| BranchShipment | `KRM-BRG` | `branch_shipments` |
| SupplierSj | `SJSUP` | `supplier_sjs` |

---

## 8. Models & Key Relations

```
ArrivalSupplierTruck
├── belongsTo: Supplier
├── belongsTo: Expedition (nullable)
├── belongsTo: User
├── hasMany: TaskTerimaSupplier
└── hasMany: TaskReturSupplier

TaskKeluarBarang
├── belongsTo: User
├── belongsTo: BranchShipment

TaskKirimanMobil
├── belongsTo: User
├── belongsTo: TaskKeluarBarang (keluar_barang_id)
└── belongsToMany: BranchShipment (pivot branch_shipment_kiriman_mobil)

BranchShipment
├── belongsTo: User
├── hasMany: TaskKeluarBarang
└── belongsToMany: TaskKirimanMobil (pivot branch_shipment_kiriman_mobil)

TaskTerimaSupplier
├── belongsTo: User
├── belongsTo: ArrivalSupplierTruck
├── belongsToMany: WarehouseEmployee (helpers pivot)
├── created/updated → syncStatus + auto-create SupplierSj
└── deleted → syncStatus revert

TaskReturSupplier
├── belongsTo: User
├── belongsTo: ArrivalSupplierTruck
├── created/updated/deleted → syncStatus

TaskReturCabang → belongsTo: User

WarehouseEmployee
├── belongsTo: Division
└── hasMany: WarehouseLeave

SupplierSj → no relations

ActivityLog → belongsTo: User
```

---

## 9. Flow: Checker Keluar → Kiriman Mobil

```
Input Kiriman Barang (BranchShipment)
  └── status=selesai
       └── Checker Keluar Barang (TaskKeluarBarang)
            ├── pilih BranchShipment → auto-fill cabang, nomor_sj, total_qty, no_po
            └── isi: jam_disiapkan, helper, status, diserahkan_kepada, keterangan
                 └── status=selesai → proses Checker selesai

Checker Kiriman input manual di menu Kiriman Mobil
└── Pilih cabang, SJ, isi plat, sopir, jam, status
```

---

## 10. Export/Import (ZipArchive)

- **SuppliersExport** — template XLSX
- **EmployeesExport** — template XLSX with division dropdown validation
- Routes: `GET /suppliers/template`, `GET /employees/template`
- **SupplierImport** — CSV/XLSX/XLS, auto-uppercase kode_supplier
- **WarehouseEmployeeImport** — CSV/XLSX/XLS, auto-create Division
- **Table Export (Custom):** `App\Services\TableExportService` — tombol **outlined kecil** (XLSX & PDF) di toolbar (sebelum search)
  - Query-based: `streamXlsx()` / `streamPdf($query, $columns, $fileName, $formatters)`
  - Array-based (custom page): `streamXlsxFromRows()` / `streamPdfFromRows()`
  - `$formatters` = `['path' => fn($record) => string]` — konversi kolom khusus (helper→nama, status→label, computed)
  - XLSX rows: **border semua sel + header bold** (OpenSpout `Style`/`Border`/`BorderPart`)
  - PDF: `exports/table-pdf` (query) / `exports/table-pdf-rows` (array, font 7px) → dompdf, A4 landscape, limit 200
  - `resolveValue()` — data_get dot-notation, format Carbon/array/bool
  - Style: `->outlined()->size(Size::Small)` + label + icon (enum `Filament\Support\Enums\Size`)
  - Pakai `getFilteredTableQuery()` → hormati filter aktif + scope role
  - **Deploy:** Datang Mobil, Terima Supplier, Input SJ, Input Kirim Barang, Checker Keluar (helper→nama), Kiriman Mobil (formatters), Cuti & Absensi (Papan Absensi matrix, border XLSX)

---

## 11. UI/UX & CSS Customizations

- **Theme:** Aurum (Gold-on-Graphite), preset **Sapphire** (biru safir)
- **Header actions:** semua tombol hijau solid, ukuran kecil via CSS
- **Helpers badge:** orange (`warning`) di semua modul + tooltip
- **Sidebar:** collapsible, groups collapsed by default, persist via localStorage
- **Compact table:** padding 2px, line-height 1.2, striped rows
- **Font:** Arial, base 14px
- **All modals:** ViewAction (Section 2 kolom) + EditAction/Create (form Section, Width::Full)
- **Single form input** — no Repeater multi-row
- **Light mode border:** input/modal/table 1.5px `#C8C4BC`
- **Footer:** text center "© 2026 jobdesk MSK. All rights reserved."

## 11.1 Dashboard Widgets (Aurum)

| Widget | Komponen | Isi | Akses |
|--------|----------|-----|-------|
| StatsOverviewWidget | `AurumStatsOverview`+`AurumStat` | Kartu per modul yang bisa di-view user (own/all via `view_all_data`); **Datang Mobil** & **Terima Barang** value `{total}/{status SELESAI}` (mis. `43/40`, `14/X`) dengan **angka selesai hijau** | Semua |
| ExpiringDocumentsWidget | `AurumValueList`+`ValueListItem` | STNK/KIR ≤7 hari / EXPIRED | Admin |
| LeavesTodayWidget | `AurumValueList`+`ValueListItem` | Cuti/Sakit/Izin hari ini + divisi | Admin |
| RecentActivityWidget | `TableWidget` | Kolom Aksi (Dibuat/Diubah/Dihapus badge+ikon), Menu badge label menu, Aktivitas readable (prettify), ID, Referensi, User, Waktu; filter Menu dinamis + User + Rentang Waktu; **pagination default 50 (opsi 50/100/200/All)** | Semua |

**Stats grid responsif (CSS):** mobile 1 kolom → tablet 2 → desktop 5 per baris (jika ≥5 kartu).
**Layout dashboard:** Stats (full) → [Expiring | Cuti] setengah-setengah → RecentActivity (full).

---

## 12. UI Modal Standards

### ViewAction (Detail)
```php
ViewAction::make()
    ->iconButton()->tooltip('Lihat Detail')->color('info')
    ->modalHeading('Detail ...')
    ->modalSubmitAction(false)
    ->modalCancelAction(fn => label('Tutup'))
    ->schema([Section::make('Informasi Task')->columns(2)->schema([...])])
```

### Edit/Create Modal
- Form dipisah jadi **beberapa Section logis** (2-4 per form)
- `->modalWidth(Width::Full)` atau `'xl'`
- Tiap Section punya judul + deskripsi: `Section::make('...')->description('...')`
- `columns(2)` sebagai default, `columns(3)` untuk grup waktu/jadwal
- `columnSpanFull()` untuk field penting (Pilih SJ, nomor_sj, keterangan)
- Disabled auto-fill fields: `->disabled()->dehydrated(true)` (data disimpan)
- Live fields: `->live()` + `afterStateUpdated` untuk auto-calc
- Helper badges: `->badge()->separator(', ')` di view / `->getStateUsing()` max 2 + tooltip di grid
- Edit dropdown disable: `->disabled(fn ($record) => $record !== null)`
- Options include current record: `->options(function ($record) { ... })`

---

## 13. Pusat Dokumen (WarehouseDocument)

- Model `WarehouseDocument` — table `warehouse_documents`
- Grup Administrasi, ikon DocumentArrowDown
- File upload ke `document_templates/`, format auto-extract
- Admin CRUD, all roles view + download
- Download action increment counter

---

## 14. Graphify Knowledge Graph

- `graphify-out/` — source code knowledge graph
- Commands: `graphify query/path/explain`, `graphify update .`
- No LLM API key — code-only extraction

---

## 15. Form Icons Standard

Semua form menggunakan `->prefixIcon('heroicon-m-...')` untuk masing-masing field:

| Modul | Field | Ikon |
|-------|-------|------|
| **Keluar Barang** | cabang | `building-storefront` |
| | branch_shipment_id | `document-arrow-down` |
| | nomor_sj | `document-text` |
| | total_qty | `cube` |
| | qty_checker | `cube` |
| | no_po | `receipt-percent` |
| | jam_disiapkan | `clock` |
| | status | `check-badge` |
| | diserahkan_kepada | `user` |
| | helper | `user-group` |
| **Kiriman Mobil** | cabang | `building-storefront` |
| | Pilih SJ | `document-text` |
| | tanggal_kirim | `calendar-days` |
| | jam_muat / selesai / berangkat / tiba | `clock` |
| | no_plat_mobil | `truck` |
| | nama_supir | `user` |
| | status | `check-badge` |
| | retur_option | `arrow-uturn-left` |

---

## 16. BranchShipment — Pilih Kiriman Options

| Value | Label | Badge |
|-------|-------|-------|
| `pembagian_po` | Pembagian PO | `info` |
| `stock_gudang` | Stock Gudang | `warning` |
| `rb_pesanan` | RB / Pesanan | `danger` |

---

## 17. Kiriman Mobil — SJ Column

Grid menampilkan SJ (`nomor_sj`) dalam bentuk badge dengan tooltip:

| Jumlah SJ | Tampilan |
|-----------|----------|
| 1 | `UBR16000014` |
| 2 | `UBR16000014` `UBR16000015` |
| 3+ | `UBR16000014` `UBR16000015` `+1 more` |

Tooltip (hover) menampilkan daftar lengkap semua SJ.

---

## 18. Kiriman Mobil — Dropdown & Fitur Lain

### No Plat Mobil
Dropdown menampilkan format `"D 8526 OE - SS BIRU"` (nomor_polisi - merek_dan_model).
Grid table tetap menampilkan hanya nomor polisi. View modal menampilkan format lengkap.

### Auto-set Status
Saat `jam_berangkat` diisi, status otomatis berubah menjadi `Dalam Pengiriman`.
Dropdown status tetap bisa diedit manual (tidak di-disabled).

### Retur Option
Hanya 2 opsi: `Tidak Ada Retur` (gray) / `Ada Retur` (warning).
Hanya muncul jika status = Selesai.

### Toggleable Columns
Semua kolom tabel Kiriman Mobil bisa di-show/hide via tombol Columns di toolbar.

---

## 19. Form UI/UX Layout Standards

### Prinsip
- Form tidak "pukul rata" — tiap Section punya kolom sendiri
- Field penting (Pilih SJ, nomor_sj, keterangan) pakai `columnSpanFull()`
- Data referensi (auto-fill) dipisah dari input manual
- Grup waktu/jadwal pakai `columns(3)` agar lebih rapat

### Pattern per Modul

| Modul | Section | Layout |
|-------|---------|--------|
| **Kiriman Mobil** | Data Kiriman (2col), Waktu Perjalanan (3col), Kendaraan & Sopir (2col), Status & Catatan (2col) | 4 Section |
| **Input Kirim Barang** | Data Kiriman (2col), Status & Tanggal (2col) | 2 Section |
| **Checker Keluar Barang** | Data SJ (2col), Tim & Status (2col) | 2 Section |
| **Datang Mobil Supplier** | Data Mobil & Supplier (2col), Waktu Kedatangan (3col), Catatan | 3 Section |
| **Input SJ Supplier** | Data Dokumen (2col), Status Input (2col) | 2 Section |

### Code Pattern
```php
Section::make('Nama Section')
    ->description('Deskripsi kontekstual')
    ->columns(2) // atau 3
    ->schema([
        // field 1 kolom
        // field 1 kolom
        TextInput::make('field_penting')
            ->columnSpanFull(),  // full width
        Textarea::make('keterangan')
            ->columnSpanFull(),
    ]),
```

---

## 20. File Structure (app/)

```
app/
├── Controllers/
│   └── TvBoardController.php        # (deleted — template not ready)
├── Exports/
│   ├── EmployeesExport.php
│   └── SuppliersExport.php
├── Filament/
│   ├── Pages/
│   │   ├── Auth/Login.php
│   │   └── ManageLeaves.php
│   ├── Resources/
│   │   ├── BranchReturnOutbound/
│   │   ├── BranchShipment/
│   │   ├── Expeditions/
│   │   ├── MasterKendaraans/
│   │   ├── MasterSopirs/
│   │   ├── MasterTokos/
│   │   ├── Suppliers/
│   │   ├── SupplierSj/
│   │   ├── SupplierReturnInbound/
│   │   ├── TaskDatangMobilSuppliers/
│   │   ├── TaskKeluarBarangs/
│   │   ├── TaskKirimanMobils/
│   │   ├── TaskReturCabangs/
│   │   ├── TaskReturSuppliers/
│   │   ├── TaskTerimaSuppliers/
│   │   ├── Users/
│   │   ├── WarehouseDocuments/
│   │   └── WarehouseEmployees/
│   └── Widgets/
│       ├── RecentActivityWidget.php
│       ├── StatsOverviewWidget.php      # AurumStatsOverview (9 kartu admin)
│       ├── ExpiringDocumentsWidget.php  # STNK/KIR ≤7 hari / EXPIRED
│       └── LeavesTodayWidget.php        # Cuti/Sakit/Izin hari ini
├── Http/Middleware/
│   ├── CheckTvBoardToken.php        # (deleted)
│   └── TrackLastActive.php          # penanda online (cache, 10 menit)
├── Imports/
│   ├── SupplierImport.php
│   └── WarehouseEmployeeImport.php
├── Models/
│   ├── ActivityLog.php
│   ├── ArrivalSupplierTruck.php
│   ├── BranchShipment.php
│   ├── Division.php
│   ├── Expedition.php
│   ├── KendaraanDokumen.php
│   ├── KomplainPo.php
│   ├── MasterKendaraan.php
│   ├── MasterSopir.php
│   ├── MasterToko.php
│   ├── Supplier.php
│   ├── SupplierReturn.php
│   ├── SupplierSj.php
│   ├── TaskKeluarBarang.php
│   ├── TaskKirimanMobil.php
│   ├── TaskReturCabang.php
│   ├── TaskTerimaSupplier.php
│   ├── TvBoardSetting.php
│   ├── User.php
│   ├── WarehouseDocument.php
│   ├── WarehouseEmployee.php
│   └── WarehouseLeave.php
│   └── Concerns/
│       └── LogsActivity.php         # auto-log create/update/delete di semua model
├── Providers/
│   └── Filament/AdminPanelProvider.php
└── Services/
    ├── ActivityLogger.php           # log + label/diff/prettify
    ├── TaskIdGenerator.php
    └── TableExportService.php
```

---

## 21. Komplain PO Module

- **Tabel:** `po_complaints` | **Model:** `KomplainPo` | **ID:** `KMPL-00001`
- **Grup:** Purchasing Order | **Akses:** RBAC `komplain_pos` (default Admin + Checker Terima via PermissionSeeder)
- **Form 3 Section:** PO Supplier (cabang, supplier, no_po, barcode) → Barang (nama, qty diterima, no_surat_jalan, qty disurat_jalan, foto max 5, tgl datang) → Status (kondisi, penyelesaian, status, keterangan)
- **Status:** `draft`/`selesai` — Selesai hanya jika `tanggal_datang_barang` terisi (select `disabled` saat kosong)
- **Kondisi:** `tidak_sesuai`/`tidak_lengkap` | **Penyelesaian:** `potong_nota`/`retur`/`ganti_barang`
- **Foto:** min 1, max 5, disk `public`, dir `fotos-komplain/`; view `ImageEntry` di modal, grid badge "X Gambar" + tooltip nama file
- **Export:** XLSX + PDF (TableExportService, formatters label)
- ⚠️ Model WAJIB `protected $table = 'po_complaints'` (bukan default `komplain_pos`)

---

## 22. Activity Logging — Otomatis Semua Menu

### Service `App\Services\ActivityLogger`
- `created($model,$module,$desc,$ref)` / `updated($model,$module,array $changes,$ref)` / `deleted(...)` / `log(...)`
- `changes($model, array $tracked)` → `['Label: old → new']` pakai `fieldLabel()` (mapping field → Bahasa Indonesia)
- `moduleLabel($module)` → key menu → **label menu** via `PermissionMenu::groups()` (fallback label lama / as-is)
- `prettifyDescription($desc)` → rapikan data lama (field Inggris → label, `—` → `;`)
- **Skip:** tidak ada `user_id` (kolom NOT NULL) dan saat `runningInConsole()` (seeder tidak banjiri log)
- `id_task` default `-` untuk modul tanpa id_task

### Trait `App\Models\Concerns\LogsActivity`
Auto-log `created/updated/deleted`. Config memakai **method static override** (bukan properti — hindari konflik trait):
`activityModule()`, `activityTracked()?array`, `activitySummaryAttributes()`, `activityReferenceField()`, `shouldLogActivity()`.

**Pasang di:** 6 task + `branch_shipments`, `supplier_sjs`, `komplain_pos`, `warehouse_documents`, `kendaraan_dokumens` (skip `user_perpanjang='System'`), `cuti_absensi` (WarehouseLeave), 6 master, `users`. Role di-log manual dari resource (`module='roles'`).

### Widget ⚡ Aktivitas Terakhir
Kolom: **Aksi** (badge+ikon `Dibuat`/`Diubah`/`Dihapus` warna success/warning/danger), **ID** (badge gray), **Menu** (badge+ikon, label via moduleLabel), **Aktivitas** (`prettify`+wrap), **Referensi**, **User**, **Waktu** (d/m/Y H:i). Filter: Menu (opsi dinamis dari distinct), User, Rentang Waktu (`from`/`until` DatePicker). Pagination [10,25,50], sort `created_at desc`.

---

## 23. Status Online User (Cache)

- `TrackLastActive` middleware → `Cache::put('user-online-{id}', true, now()->addMinutes(10))` di tiap request authenticated panel
- `User::isOnline()` → `Cache::has(...)`; tanpa migration baru
- Kolom **Status** di menu Users: badge hijau `Online` (wifi) / abu `Offline` (signal-slash); threshold 10 menit

---

## 24. Modul User & Role — Modal (bukan halaman)

- `UserResource`/`RoleResource` `getPages()` hanya `index`; Create/Edit via **modal** action
- Form ber-**Tabs** (`columnSpanFull`) — mengatasi `ListRecords::getDefaultActionSchemaResolver()` yang mengepak schema `columns(2)`
- `PermissionMenu`: `globalSection` / `menuSections` (group collapsible 2 kolom, Fieldset 5 kolom) / `widgetsSections`
- Sync role+permission di `using()` menjadikan `$data` (checkbox `perm_*` dehydrated)
- Kelola hanya oleh **Super Admin**; modal `Width::Full`, tanpa `createAnother`

---

## 25. Fixes Terakhir

- Header grup Masa Berlaku STNK/KIR: mojibake emoji bersih → `STNK 1 Tahun` / `STNK 5 Tahun (Acuan)` / `KIR`; group heading **bold** (`.fi-ta-group-header .fi-ta-group-heading`)
- Gate "Atur Saldo Cuti" di `ManageLeaves` via permission `update_cuti_absensi` (visible + guard server)
- Sidebar lebar FIXED (`--sidebar-width` 14rem) untuk semua role; collapsed state disimpan per-browser (`localStorage`)
- Komplain PO: `Tanggal Datang Barang` → **`Tanggal Penyelesaian`** (form/grid/view/export)

## 26. Modal & Form — Standar 75% + Blok Fieldset

- **Standar lebar modal create/edit:** `Width::SevenExtraLarge` (≈75%) untuk Retur Masuk dari Toko, Retur In & Out Supplier, Datang Mobil Supplier. `Width::Full` hanya utk User/Role/Pusat Dokumen.
- **Blok `Fieldset` kondisional:** ganti field tersebar `visible()` → blok Fieldset tampil sesuai pilihan `live()` (Retur Bagus/Jelek, Retur Keluar/Masuk), berdampingan (Grid 2) saat tipe campuran, satu blok penuh saat single.
- **Grid dalam Section:** wrapper `Grid::make(...)` wajib `->columnSpanFull()` agar tidak terjepit 1 kolom.
- Retur Supplier: **`Jenis Pengiriman*`** di awal **Detail Retur**; **`Potong Nota`** milik **Retur Keluar** (Retur Masuk = Servis/Ganti Barang).

## 27. Integrasi Status Truk (Datang–Terima–Retur–SJ)

- `ArrivalSupplierTruck::syncStatus()` — truk **SELESAI** bila semua kewajiban per `jenis_kiriman` beres:
  - `DATANG` → butuh `TaskTerimaSupplier` SELESAI (+ `selesai_bongkar`)
  - `RETUR` → butuh `SupplierReturn` status `selesai`
  - `DATANG & RETUR` → **keduanya** wajib selesai
  - retur masih draft/belum → truk `PROSES`; tanpa terima & retur → `MENGANTRI`
- Trigger sync: `TaskTerimaSupplier` dan `SupplierReturn` (created/updated/deleted) → `syncStatus()`; guard hapus truk untuk keduanya
- Field `status` di form Datang dihapus (otomatis; baru = `MENGANTRI`)

## 28. Modul Retur & SJ — Hard Break

- **Retur Masuk dari Toko:** `task_retur_cabangs.kiriman_mobil_id` (nullable FK, backfill unik); dropdown kiriman `whereDoesntHave` → terpakai tidak muncul; autofill nullable + fallback
- **Tanggal Bongkar** `maxDate(now())` + rule `before_or_equal:today` (picker & server-side tolak masa depan; create & edit)
- **Input SJ:** data otomatis dari `TaskTerimaSupplier` SELESAI (tanpa tombol Tambah & tanpa DeleteBulk); Terima jadi draft/dihapus → SJ ikut hapus; Edit `Selesai` wajib No PO (`ValidationException`, modal tetap buka)

## 29. Filter Supplier Retur + Widget Datang Mobil 43/40

- **Retur In & Out Supplier:** filter **Supplier** (SelectFilter searchable, opsi dari `nama_supplier` distinct) di **posisi pertama** (sebelum Jenis); `filtersFormColumns(3)` → 1 baris `Supplier | Jenis | Tanggal Buat`
- **Widget Datang Mobil:** value `total/selesai` — `(clone $query)->where('status','SELESAI')->count()`; angka selesai dibungkus `<span class="aurum-stat-value--green">` (CSS `color:#22c55e`)
- **Override blade** `resources/views/vendor/aurum-filament-theme/widgets/stats-overview.blade.php`: value di-render `{!! !!}` (HTML-safe, value hanya dari kode widget); deskripsi tetap escaped — pola untuk styling value per-kartu tanpa sentuh vendor

## 30. Plugin Team Chat (PENDING)

- **Plugin:** `qalainau/filament-team-chat` — Slack-like chat di panel (channels, DM, threads, reactions, @mention, file, unread); **kompatibel** Filament v5.x / Laravel 13 / PHP ^8.3; zero dependency eksternal (Livewire polling)
- **Cara register:** `FilamentTeamChatPlugin::make()` → page `Filament\TeamChat\Pages\TeamChat` (slug `team-chat`); `HasTeamChat` trait di `User`; tabel `tc_*`
- **Gate akses RBAC:** permission `view_team_chat` di Users role; middleware `EnsureTeamChatAccess` (route name `.team-chat` tanpa izin → 403); hide nav via renderHook CSS (`a[href$="/team-chat"]{display:none}`) utk user tanpa izin
- **⚠️ Tailwind:** class plugin harus ter-compile — verifikasi CSS panel mana yang diload (panel pakai Aurum plugin; tambah `@source vendor/qalainau/.../views/**/*` di theme yang benar, lalu `npm run build`)
- **Risiko:** Beta, health 70/100 (komunitas); polling 3–5 dtk
