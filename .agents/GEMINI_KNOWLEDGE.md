# Project Context: Jobdesk Gudang AP

**Versi:** 1.2 | **Tanggal:** 20 Juli 2026

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
| Auth | Spatie Laravel Permission | 5 role |
| Frontend | Tailwind CSS + Alpine.js | Bundled via Filament |
| Assets | Vite | `npm run dev` atau `npm run build` |
| Export/Import | ZipArchive (native PHP) | Tidak pakai maatwebsite/phpspreadsheet |
| Code Graph | Graphify | `graphify-out/` — 5800+ nodes, 18000+ edges |

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

### .env Wajib
```env
DB_CONNECTION=mysql
DB_DATABASE=jobdesk_gudang
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### Testing
```bash
composer test   # php artisan config:clear && php artisan test
```
PHPUnit 12 (no Pest).

### Pull di PC Kantor (Windows/Linux)
```bash
git pull
composer install --no-dev
php artisan migrate
php artisan optimize
npm run build
```

---

## 4. Navigation Groups

| Group | Menu | Ikon | Role Access |
|-------|------|------|-------------|
| **Dashboard** (no group) | Dasbor | home | Semua role |
| **Master** | Master Ekspedisi | BuildingOffice2 | Admin |
| | Master Kendaraan | Truck | Admin |
| | Master Sopir | Users | Admin |
| | Master Toko | BuildingStorefront | Admin |
| | Master Supplier | BuildingStorefront | Admin |
| | Master Employee Gudang | UserGroup | Admin |
| **Retur** | Retur Masuk dari Cabang | ArrowPath | Admin, Checker Retur |
| | Retur Keluar ke Supplier | ArrowUpOnSquare | Admin, Checker Retur |
| | Retur Masuk dari Supplier | ArrowDownOnSquare | Semua |
| | Retur Keluar untuk Cabang | PaperAirplane | Semua |
| **Penerimaan** | Input SJ dari Supplier | DocumentText | Semua |
| | Datang Mobil Supplier | Truck | Admin, Checker Terima |
| | Checker Terima Barang Supplier | ClipboardDocumentList | Admin, Checker Terima |
| **Pengiriman** | Checker Keluar Barang | ClipboardDocumentCheck | Admin, Checker Keluar |
| | Kiriman Mobil | Truck | Admin, Checker Kiriman |
| **Administrasi** | Cuti & Absensi | CalendarDays | Admin |
| **Pengaturan** | Users | RectangleStack | Admin |

---

## 5. Roles & Access

| Role | Hak Akses |
|------|-----------|
| **Admin** | Full — semua menu, semua data semua user, CRUD user, delete all records |
| **Checker Retur** | Retur Masuk Cabang, Retur Keluar Supplier — hanya data sendiri, tidak bisa delete |
| **Checker Terima** | Datang Mobil, Terima Barang Supplier — hanya data sendiri, tidak bisa delete |
| **Checker Keluar** | Keluar Barang — hanya data sendiri, tidak bisa delete |
| **Checker Kiriman** | Kiriman Mobil — hanya data sendiri, tidak bisa delete |

### Pattern Role Access
```php
canViewAny()           → hasRole('Admin') || hasRole('Checker X')
canDelete()            → only Admin
getEloquentQuery()     → where('user_id', auth()->id()) for non-Admin
shouldRegisterNavigation() → hasRole('Admin') || hasRole('Checker X')
```

### Seeder
`database/seeders/RoleSeeder.php` — 5 role: Admin, Checker Retur, Checker Terima, Checker Keluar, Checker Kiriman
`database/seeders/DatabaseSeeder.php` — seed role lalu buat user `admin@jobdesk.test` / `password` dengan role Admin.

---

## 6. Database Schema

### Task Tables (6) — Log Harian

**Task Retur Supplier** (`task_retur_suppliers`)
`id_task, nama_supplier_ekspedisi, no_plat_mobil, nama_sopir, jam_muat, jumlah_kolian, admin_sj_retur, status (servis/tukar/pot_nota), keterangan, user_id`

**Task Retur Cabang** (`task_retur_cabangs`)
`id_task, cabang, jenis_retur (retur_jelek/retur_bagus), no_sj_retur, total_kolian, jam_bongkar, nama_sopir, keterangan, user_id`

**Task Terima Supplier** (`task_terima_suppliers`)
`id_task, nama_supplier_ekspedisi, no_po_referensi, jam_datang, jumlah_kolian, jam_bongkar, selesai_bongkar, lembar_sj, nama_sopir, status (komplit/kurang/lebih), keterangan, user_id`

**Task Keluar Barang** (`task_keluar_barangs`)
`id_task, toko_tujuan, supplier, no_referensi_sj, jumlah_kolian, jam_naik, nama_koordinator, status (komplit/kurang/lebih), keterangan, user_id`

**Task Kiriman Mobil** (`task_kiriman_mobils`)
`id_task, cabang, no_plat_mobil, jam_muat, jam_selesai_muat, jam_berangkat, nama_supir, keterangan, user_id`

**Arrival Supplier Trucks** (`arrival_supplier_trucks`)
`id_task, supplier_id (FK), expedition_id (FK, nullable), nama_sopir, tanggal_datang, no_plat_mobil, jam_datang, jam_selesai, keterangan, user_id`

Semua task table: `id_task` indexed (not unique), `user_id` FK ke users.

### Master Tables (7)

**Suppliers** (`suppliers`) — `kode_supplier (unique), nama_supplier, alamat, no_telepon, keterangan`
**Expeditions** (`expeditions`) — `nama_ekspedisi, no_telepon, alamat`
**Master Sopir** (`master_sopirs`) — `nama_sopir, no_whatsapp`
**Master Toko** (`master_tokos`) — `nama_toko, alamat`
**Master Kendaraan** (`master_kendaraans`) — `nomor_polisi, jenis_kendaraan, merek_dan_model, nomor_rangka, nomor_mesin, no_stnk, no_kir, masa_berlaku_stnk, masa_berlaku_kir, keterangan`
**Warehouse Employees** (`warehouse_employees`) — `nama_karyawan, no_whatsapp, division_id (FK), jatah_cuti (default 12)`
**Divisions** (`divisions`) — `nama_divisi (unique), keterangan`

### Support Tables (4)

**Activity Logs** (`activity_logs`) — `user_id, module, id_task, description, reference, action`
**Warehouse Leaves** (`warehouse_leaves`) — `warehouse_employee_id (FK), jenis_absen (Cuti/Sakit/Izin), tanggal_mulai, tanggal_selesai, keterangan, user_id`
**Supplier SJs** (`supplier_sjs`) — `nama_supplier, tanggal_datang, nomor_po_referensi, status_input (kosong/sudah), tanggal_input, keterangan`
**Supplier Return Inbounds** (`supplier_return_inbounds`) — `nama_supplier, nama_ekspedisi, nama_supir, no_plat_mobil, tanggal_datang, jam_kedatangan, no_nota_retur, jumlah_kolian, keterangan`
**Branch Return Outbounds** (`branch_return_outbounds`) — `toko_tujuan, nomor_sj, total_qty, disiapkan_oleh, jam_naik, diserahkan_kepada, status, keterangan`

### Spatie Tables
`permissions, roles, model_has_permissions, model_has_roles, role_has_permissions`

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

Counter per-prefix, sequential, tidak di-reset per hari.

---

## 8. Models & Key Relations

```
WarehouseEmployee
├── belongsTo: Division
└── hasMany: WarehouseLeave (via warehouse_employee_id)

WarehouseLeave
├── belongsTo: WarehouseEmployee (employee)
└── belongsTo: User

ArrivalSupplierTruck
├── belongsTo: Supplier
├── belongsTo: Expedition (nullable)
└── belongsTo: User

TaskKeluarBarang → belongsTo: User
TaskKirimanMobil → belongsTo: User
TaskReturCabang → belongsTo: User
TaskReturSupplier → belongsTo: User
TaskTerimaSupplier → belongsTo: User

Division → hasMany: WarehouseEmployee

ActivityLog → belongsTo: User
Supplier → no relations (standalone)
Expedition → no relations (standalone)
MasterKendaraan → no relations (standalone)
MasterSopir → no relations (standalone)
MasterToko → no relations (standalone)
SupplierSj → no relations (standalone)
SupplierReturnInbound → no relations (standalone)
BranchReturnOutbound → no relations (standalone)
```

---

## 9. Export/Import

### Export (ZipArchive XLSX)
- **SuppliersExport** — template download, headers: kode_supplier*, nama_supplier*, alamat, no_telepon, keterangan
- **EmployeesExport** — template download, headers: Nama Karyawan, No WhatsApp, Divisi Gudang (dropdown validation)
- Route: `GET /suppliers/template`, `GET /employees/template`

### Import (CSV/XLSX/XLS)
- **SupplierImport** — reads CSV/XLSX/XLS, validates kode_supplier unique, uppercase kode_supplier
- **WarehouseEmployeeImport** — reads CSV/XLSX/XLS, auto-create Division by nama_divisi, skip duplicate nama_karyawan

### All native PHP (ZipArchive + SimpleXML + DOMDocument)
PHP 8.5.8 incompatible with maatwebsite/excel and phpoffice/phpspreadsheet.

---

## 10. Custom Pages

### Cuti & Absensi (`ManageLeaves.php`)
- URL: `admin/cuti-absensi`
- Grup: Administrasi
- Monthly attendance matrix (karyawan x tanggal)
- 2 Tabs: Papan Absensi (matrix grid + filter) | Atur Saldo Cuti (tabel jatah cuti + adjust)
- Validasi: minDate (no backdate), no duplicate, max 12 Cuti/tahun (by jatah_cuti)
- Sisa cuti: `jatah_cuti - totalCutiDipake`

### Login (`Login.php`)
- Custom login page dengan logo

---

## 11. Dashboard Widgets

### StatsOverviewWidget
- Admin: 5 stat cards (Retur Supplier, Retur Cabang, Terima Barang, Keluar Barang, Kiriman Mobil) — total all users
- Checker Retur: 2 cards filtered by user_id
- Checker Terima/Keluar/Kiriman: 1 card each filtered by user_id

### RecentActivityWidget
- 10 recent activity logs with user, module badge (color-coded), description, timestamp
- Filter by module, pagination [10, 25, 50]
- Column: user.name, description (wrap), reference, module (badge), created_at

---

## 12. UI/UX & CSS Customizations

- **Primary color:** `#EA580C` (orange)
- **Sidebar:** collapsible on desktop, width 14rem, groups collapsed by default via `Alpine.store('sidebar').collapsedGroups`
- **Compact table:** `padding-top: 2px`, `padding-bottom: 2px`, `line-height: 1.2`
- **Striped rows:** odd rows get background `rgba(249,250,251,0.5)` light mode / `rgba(255,255,255,0.04)` dark mode
- **Table borders:** header cells `rgba(128,128,128,0.18)`, data cells `rgba(128,128,128,0.10)`
- **Sidebar border:** right border `rgba(128,128,128,0.15)`
- **Font:** Arial (local), base font-size: 14px
- **Sidebar labels:** word-break enable (tidak terpotong)
- **Input time:** min-width 8rem
- **Repeater animation:** fi-row-enter fade-in 0.25s

---

## 13. Branch Return Outbound (Retur Keluar Cabang)

- Model `BranchReturnOutbound` — table `branch_return_outbounds`
- Kolom: `toko_tujuan, nomor_sj, total_qty, disiapkan_oleh, jam_naik, diserahkan_kepada, status, keterangan` — semua nullable
- Navigation: Grup Retur, ikon PaperAirplane, label 'Retur Keluar untuk Cabang'
- Form 2 kolom, no relation, no ID task

---

## 14. Supplier Return Inbound (Retur Masuk Supplier)

- Model `SupplierReturnInbound` — table `supplier_return_inbounds`
- Kolom: `nama_supplier, nama_ekspedisi, nama_supir, no_plat_mobil, tanggal_datang, jam_kedatangan, no_nota_retur, jumlah_kolian, keterangan` — semua nullable
- Navigation: Grup Retur, ikon ArrowDownOnSquare, label 'Retur Masuk dari Supplier'
- Form 2 kolom, no relation, no ID task

---

## 15. Datang Mobil Supplier — Fitur Lengkap

### Fields
- `jenis_kiriman` (string: DATANG / RETUR / DATANG & RETUR)
- `status` (ENUM: **MENGANTRI** ⬜ → **PROSES** 🟡 → **SELESAI** 🟢, default MENGANTRI)

### Auto-Sync Logic (`syncStatus()`)
Di-trigger dari `created`/`updated`/`deleted` event pada TaskTerimaSupplier dan TaskReturSupplier.

Logika:
- Jika tidak ada TaskTerimaSupplier atau TaskReturSupplier → **MENGANTRI**
- Jika ada aktivitas tapi belum selesai → **PROSES**
- Jika Terima SELESAI + selesai_bongkar terisi DAN (Retur selesai jika jenis kiriman RETUR/DATANG & RETUR) → **SELESAI**
- `jam_selesai` = max(selesai_bongkar, jam_muat_retur)

---

## 16. Checker Terima Supplier — Fitur Integrasi

### FK Arrival Supplier Truck
- Kolom `arrival_supplier_truck_id` FK ke `arrival_supplier_trucks`
- Select reactive: pilih mobil datang → autofill: `nama_supplier_ekspedisi`, `jam_datang`, `nama_sopir` (disabled)
- Filter dropdown: hanya mobil dengan `status = 'PROSES'` DAN `jenis_kiriman IN ('DATANG', 'DATANG & RETUR')`

### Helpers (Pivot)
- Tabel pivot: `task_terima_supplier_helpers` (task_terima_supplier_id, warehouse_employee_id)
- Select multiple dengan `options()` dari `WarehouseEmployee::pluck()`
- Save: `unset($data['helpers'])` + `$record->helpers()->sync($helpers)`
- Tampilkan di grid sebagai badge hijau + modal detail

### Status Enum
- Sebelum: `('komplit', 'kurang', 'lebih')` NOT NULL
- Sesudah: `('selesai_tanpa_retur', 'selesai_ada_retur')` NULL DEFAULT NULL
- Data lama: komplit/kurang/lebih → selesai (tanpa_retur)

---

## 17. Input SJ dari Supplier

- Model `SupplierSj` — table `supplier_sjs`
- Kolom: `nama_supplier, tanggal_datang, nomor_po_referensi, status_input (kosong/sudah), tanggal_input, keterangan`
- Navigation: Grup Penerimaan, ikon DocumentText
- Single form, no ID task

---

## 18. Data Integrity & Protection

### TaskTerimaSupplier `deleted` event
- Saat `TaskTerimaSupplier` dihapus → revert `ArrivalSupplierTruck.status` ke `'PROSES'`, `jam_selesai` ke `null`
- Method: `static::deleted(function ($model) { ... })`

### ArrivalSupplierTruck `deleting` event
- Sebelum hapus, cek `TaskTerimaSupplier::where('arrival_supplier_truck_id', $this->id)->exists()`
- Jika ada → `throw ValidationException::withMessages(...)` — data tidak bisa dihapus
- Method: `static::deleting(function ($model) { ... })`

### Edit Mode Protection
- `arrival_supplier_truck_id` dropdown **disabled** saat Edit (via `->disabled(fn ($cmp) => $cmp->getRecord() !== null)`)
- Select pakai `->options()` closure: include record yg sedang diedit + filter `status = 'PROSES'`

### Helpers Grid Display
- Max 2 nama helpers + `+N more` dalam green badge
- Tidak melebar vertikal

---

## 20. Pusat Dokumen (WarehouseDocument)

- Model `WarehouseDocument` — table `warehouse_documents`
- Kolom: `nama_dokumen, kategori (Formulir Lapangan/SOP Gudang/Template Import), versi, file_path, format_file, deskripsi, download_count, user_id`
- Navigation: Grup **Administrasi**, ikon `DocumentArrowDown`, label 'Pusat Dokumen'
- **Role Access:** Admin bisa CRUD, semua Checker bisa lihat + download (tidak bisa create/edit/delete)
- **File Upload:** `FileUpload` ke `document_templates/`, accepted: pdf, csv, xls/xlsx, ods, ppt, txt, jpg/jpeg, png (max 10MB)
- **Format File:** Auto-set dari `pathinfo($file_path, PATHINFO_EXTENSION)` di action create (bukan `afterStateUpdated`)
- **Grid:** striped, badge kategori (info/warning/success) + badge format (danger/success/info/warning) + download count
- **Download Action:** Increment `download_count` + `Storage::disk('local')->download($record->file_path)`
- **Edit modal:** `->disabled()` untuk format_file (auto)

---

## 21. BranchShipment (Input Kirim Barang)

- Model `BranchShipment` — table `branch_shipments`
- Kolom: `id_task, pilih_kiriman (pembagian_po/stock_gudang), cabang, nomor_sj, total_qty, no_po, tanggal_buat, status (draft/selesai), keterangan, user_id`
- **ID Task:** Prefix `KRM-BRG` — auto `KRM-BRG-00001` via `TaskIdGenerator`
- Navigation: Grup **Pengiriman**, ikon `PaperAirplane`, label 'Input Kirim Barang'
- **Role Access:** Admin + Checker Keluar (sama seperti Keluar Barang)
- **Form:** Section 2 kolom, create/edit modal full width
- **ViewAction:** Section 2 kolom, tombol Tutup (seragam dengan modul lain)
- **Grid:** Striped, badge pilih_kiriman (info/warning) + status (warning/success) + id_task

## 19. UI Modal Standards

### ViewAction (Detail)
Semua modul menggunakan template seragam:
```php
ViewAction::make()
    ->iconButton()
    ->tooltip('Lihat Detail')
    ->color('info')
    ->modalHeading('Detail ...')
    ->modalSubmitAction(false)
    ->modalCancelAction(fn (Action $a) => $a->label('Tutup'))
    ->schema([
        Section::make('Informasi ...')->columns(2)->schema([
            TextEntry::make(...),
        ]),
    ]),
```

### Create/Edit Modal
- Form dalam `Section` + `columns(3)` untuk 3 kolom field
- Field disabled dari relasi (autofill) → `->disabled()->dehydrated(true)`
- Select: `->searchable()->preload()` untuk UX cepat
- Modal width: `Width::Full` untuk 3-kolom form; default untuk detail

---

## 22. SupplierSj — Auto-Create & Fitur

### Auto-Creation dari TaskTerimaSupplier
Di `TaskTerimaSupplier` model (created + updated event):
- Trigger: ketika `status === 'SELESAI'`
- Data: `nama_supplier`, `tanggal_datang` dari ArrivalSupplierTruck
- `no_po_referensi` dari `TaskTerimaSupplier.no_po_referensi`
- `jumlah_koli` dari `jumlah_kolian`, `jumlah_faktur` dari `lembar_sj`
- `status_input` default `'belum_di_cek'`
- Cegah duplikat via `where('keterangan', 'LIKE', '%id_task%')`

### Status Input (baru)
| Value | Label | Badge |
|-------|-------|-------|
| `belum_di_cek` | Belum Di Cek | `gray` |
| `draft` | Draft | `warning` |
| `selesai` | Selesai | `success` |

### Tempo (Virtual Column)
- **Rumus:** `abs(hari_ini - tanggal_datang)`
- **Badge:** merah (belum_di_cek/draft), hijau (selesai)
- Format: `blm input X hr` / `input X hr`

### Edit Form
- Section "Informasi Dokumen" + Width::Full
- Disabled fields: ID Task, Supplier, Tgl Datang, No PO, Koli, Faktur, Tempo, Ref Terima
- Editable: Status Input, Tanggal Input, Keterangan
- `tanggal_input` → `maxDate(now())` — tidak boleh tanggal maju

---

## 23. Lama Bongkar (Virtual Column — Checker Terima Supplier)

### Perhitungan
- **Rumus:** `selesai_bongkar - jam_bongkar` dalam menit
- **Format:** `3j 30m` atau `45m` (grid), `3 jam 30 menit` (ViewAction)
- Jika `selesai_bongkar` null → tampil `-`

### 3 Lokasi
| Lokasi | Tipe | Format |
|--------|------|--------|
| Grid kolom | `TextColumn` | `3j 30m` |
| ViewAction | `TextEntry` | `3 jam 30 menit` |
| Edit Form | `TextInput` disabled | `3j 30m` (icon jam) |

## 15. Graphify Knowledge Graph

Project memiliki knowledge graph di `graphify-out/`:
- **Graph JSON:** `graphify-out/graph.json` (5800+ nodes, 18000+ edges)
- **Graph Report:** `graphify-out/GRAPH_REPORT.md`
- **Commands:**
  - `graphify query "<question>"` — cari node dan relasi
  - `graphify path "<A>" "<B>"` — shortest path antara 2 node
  - `graphify explain "<concept>"` — penjelasan node + neighbors
  - `graphify update .` — update setelah perubahan kode (post-commit hook otomatis)
- No LLM API key set — code-only extraction.

---

## 16. File Structure (app/)

```
app/
├── Exports/
│   ├── EmployeesExport.php      # XLSX template employee
│   └── SuppliersExport.php      # XLSX template supplier
├── Filament/
│   ├── Pages/
│   │   ├── Auth/
│   │   │   └── Login.php
│   │   └── ManageLeaves.php
│   ├── Resources/
│   │   ├── BranchReturnOutbound/  # Retur Keluar untuk Cabang
│   │   ├── Expeditions/           # Master Ekspedisi
│   │   ├── MasterKendaraans/      # Master Kendaraan
│   │   ├── MasterSopirs/          # Master Sopir
│   │   ├── MasterTokos/           # Master Toko
│   │   ├── Suppliers/             # Master Supplier + import
│   │   ├── SupplierSj/            # Input SJ dari Supplier
│   │   ├── SupplierReturnInbound/ # Retur Masuk dari Supplier
│   │   ├── TaskDatangMobilSuppliers/  # Datang Mobil Supplier
│   │   ├── TaskKeluarBarangs/         # Checker Keluar Barang
│   │   ├── TaskKirimanMobils/         # Kiriman Mobil
│   │   ├── TaskReturCabangs/          # Retur Masuk dari Cabang
│   │   ├── TaskReturSuppliers/        # Retur Keluar ke Supplier
│   │   ├── TaskTerimaSuppliers/       # Checker Terima Barang Supplier
│   │   ├── Users/                     # Users
│   │   └── WarehouseEmployees/        # Master Employee Gudang
│   └── Widgets/
│       ├── RecentActivityWidget.php
│       └── StatsOverviewWidget.php
├── Imports/
│   ├── SupplierImport.php
│   └── WarehouseEmployeeImport.php
├── Models/
│   ├── ActivityLog.php
│   ├── ArrivalSupplierTruck.php
│   ├── BranchReturnOutbound.php
│   ├── Division.php
│   ├── Expedition.php
│   ├── MasterKendaraan.php
│   ├── MasterSopir.php
│   ├── MasterToko.php
│   ├── Supplier.php
│   ├── SupplierReturnInbound.php
│   ├── SupplierSj.php
│   ├── TaskKeluarBarang.php
│   ├── TaskKirimanMobil.php
│   ├── TaskReturCabang.php
│   ├── TaskReturSupplier.php
│   ├── TaskTerimaSupplier.php
│   ├── User.php
│   ├── WarehouseEmployee.php
│   └── WarehouseLeave.php
├── Providers/
│   └── Filament/
│       └── AdminPanelProvider.php
└── Services/
    └── TaskIdGenerator.php
```
