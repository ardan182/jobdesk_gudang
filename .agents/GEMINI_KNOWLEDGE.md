# Project Context: Jobdesk Gudang AP

**Versi:** 1.4 | **Tanggal:** 24 Juli 2026

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
| **Pengiriman** | Input Kirim Barang | PaperAirplane | Admin, Checker Keluar |
| | Checker Keluar Barang | ClipboardDocumentCheck | Admin, Checker Keluar |
| | Kiriman Mobil | Truck | Admin, Checker Kiriman |
| **Administrasi** | Cuti & Absensi | CalendarDays | Admin |
| | Pusat Dokumen | DocumentArrowDown | Admin (CRUD), all (view) |
| **Pengaturan** | Users | RectangleStack | Admin |
| | Pengaturan Board TV | tv | Admin |

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

---

## 11. UI/UX & CSS Customizations

- **Primary color:** `#EA580C` (orange)
- **Sidebar:** collapsible, groups collapsed by default, persist via localStorage
- **Compact table:** padding 2px, line-height 1.2, striped rows
- **Font:** Arial, base 14px
- **All modals:** ViewAction (Section 2 kolom) + EditAction/Create (form Section, Width::Full)
- **Single form input** — no Repeater multi-row

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
- `->modalWidth(Width::Full)` atau `'xl'`
- Form dalam `Section` + `columns(2)` atau `columns(4)`
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

## 15. File Structure (app/)

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
│       └── StatsOverviewWidget.php
├── Http/Middleware/
│   └── CheckTvBoardToken.php        # (deleted)
├── Imports/
│   ├── SupplierImport.php
│   └── WarehouseEmployeeImport.php
├── Models/
│   ├── ActivityLog.php
│   ├── ArrivalSupplierTruck.php
│   ├── BranchReturnOutbound.php
│   ├── BranchShipment.php
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
│   ├── WarehouseDocument.php
│   ├── WarehouseEmployee.php
│   └── WarehouseLeave.php
├── Providers/
│   └── Filament/AdminPanelProvider.php
└── Services/
    └── TaskIdGenerator.php
```
