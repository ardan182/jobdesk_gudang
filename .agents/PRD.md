# PRD — Jobdesk Gudang AP

**Versi:** 2.8 | **Tanggal:** 9 Agustus 2026

---

## 1. Ringkasan

Aplikasi pencatatan jobdesk harian gudang berbasis web. Digitalisasi log harian — retur, penerimaan, pengiriman barang — dengan multi-user role-based access via Filament v5 admin panel. Plus modul Cuti & Absensi, master data dengan import/export, dan integrasi antar modul.

---

## 2. Fitur Utama

### 2.1 Manajemen Master Data
- **7 Master Module:** Ekspedisi, Kendaraan, Sopir (+no_whatsapp), Toko, Supplier (import XLSX/XLS/CSV), Employee Gudang (import + Divisions Tabs), Divisions
- **Divisions:** Kelola divisi via widget di Employee Gudang
- **Import:** Supplier & Employee via upload CSV/XLSX/XLS (ZipArchive, tanpa library eksternal)
- **Export Template:** Download template XLSX via route `suppliers/template`, `employees/template`
- **Export Data:** Custom export XLSX & PDF — icon-only button di toolbar (sejajar sebelum search), download langsung sesuai filter aktif
  - `TableExportService` — reusable service (OpenSpout XLSX + dompdf PDF)
  - Menghormati filter AboveContent + scope role (`getFilteredTableQuery()`)
  - XLSX unlimited (chunk stream), PDF max 200 baris

### 2.2 Modul Task (Log Harian) — Single Form

| Modul | Prefix | Role | Grup |
|-------|--------|------|------|
| Retur Masuk dari Cabang | RET-CAB | Checker Retur | Retur |
| Retur Keluar ke Supplier | RET-SUP | Checker Retur | Retur |
| Datang Mobil Supplier | ARR-SUP | Checker Terima | Penerimaan |
| Checker Terima Barang Supplier | TRM-SUP | Checker Terima | Penerimaan |
| Checker Keluar Barang | KLR | Checker Keluar | Pengiriman |
| Kiriman Mobil | KRM | Checker Kiriman | Pengiriman |

Semua modul: **single form modal** (tanpa Repeater multi-row), **ID_TASK** auto format `PREFIX-NNNNN`, ViewAction + EditAction iconButton, **hanya Admin yang bisa delete**.

### 2.3 Modul Non-Task (Single Form)

| Modul | Grup | Keterangan |
|-------|------|------------|
| Input SJ dari Supplier | Penerimaan | Input surat jalan, status belum di cek/draft/selesai |
| Input Kirim Barang | Pengiriman | BranchShipment — data source untuk Checker Keluar |
| Retur Masuk dari Supplier | Retur | Log retur masuk dari supplier |
| Retur Keluar untuk Cabang | Retur | Log retur keluar ke cabang |

### 2.4 Datang Mobil Supplier — Fitur Lengkap
- **jenis_kiriman:** DATANG, RETUR, DATANG & RETUR
- **status:** MENGANTRI → PROSES → SELESAI (auto-sync)
- Auto-sync: saat `selesai_bongkar` terisi di Terima Supplier → `jam_selesai` + `status=SELESAI` di Datang Mobil

### 2.5 Checker Terima Supplier — Fitur Baru
- **FK ke `arrival_supplier_trucks`:** Pilih mobil datang, autofill supplier/sopir/jam
- **Helpers (pivot):** Multi-select karyawan pembantu via `task_terima_supplier_helpers`
- **Status:** DRAFT / SELESAI
- **Filter dropdown:** Hanya mobil dengan status PROSES + jenis DATANG / DATANG & RETUR
- **Tampilkan:** Jenis Kiriman, ID Task Mobil (info), Helpers (badge) di grid + modal detail

### 2.6 Checker Keluar Barang — Flow Integrasi
- **Data diambil dari Input Kiriman Barang (BranchShipment):** Pilih BranchShipment status=selesai
- **Auto-fill:** cabang, nomor_sj, total_qty, no_po — disimpan di tabel sendiri (dehydrated)
- **Field baru:** jam_disiapkan, diserahkan_kepada (textbox), helper (JSON array WarehouseEmployee)
- **Status:** draft → siap kirim → selesai
- **Kiriman Mobil dibuat manual** oleh Checker Kiriman (tidak auto-create)

### 2.7 Kiriman Mobil — Multi SJ + Status
- **Relasi many-to-many dengan BranchShipment:** pivot `branch_shipment_kiriman_mobil`
- **Pilih SJ:** Select multiple filter by cabang, tampilkan total & sisa SJ
- **Field baru:** jam_tiba, tanggal_kirim (nullable, no backdate), status (draft/dalam pengiriman/selesai)
- **durasi_kiriman:** computed (jam_tiba - jam_berangkat), display-only
- **Nullable fields:** no_plat_mobil, jam_muat, jam_selesai_muat, jam_berangkat, nama_supir
- **Dropdown no_plat_mobil:** format "no plat - merek & model"
- **Auto-set status:** saat jam_berangkat diisi → status otomatis "Dalam Pengiriman"
- **Retur option:** 2 opsi — "Tidak Ada Retur" / "Ada Retur"
- **Checker Kiriman input manual** (tidak auto-create dari Checker Keluar)

### 2.8 Cuti & Absensi
- Halaman `ManageLeaves` di grup **Administrasi**
- **Tabs:** Papan Absensi (matrix grid) + Atur Saldo Cuti (jatah cuti per karyawan)
- **Filter:** Bulan, Tahun, Divisi, Hanya yang absen
- **Validasi:** minDate (no backdate), no duplicate, max jatah_cuti/tahun
- **Sisa Cuti:** `jatah_cuti - totalCutiDipakai` (warna merah jika 0)

### 2.9 Dashboard
- **StatsOverviewWidget** — AurumStatsOverview + AurumStat, full width
  - **Admin (9 kartu):** Retur ke/dari Supplier, Terima, Keluar, Kiriman, Retur Masuk Cabang, Datang Mobil, SJ Belum Di Cek, STNK/KIR ≤30 hari
  - **Checker:** kartu sesuai role (data sendiri)
  - Grid responsif: mobile 1 kolom, tablet 2, desktop 5 per baris
- **ExpiringDocumentsWidget** — AurumValueList, STNK/KIR ≤7 hari atau EXPIRED (Admin only, setengah halaman, klik → menu STNK/KIR)
- **LeavesTodayWidget** — AurumValueList, Cuti/Sakit/Izin hari ini + divisi (Admin only, setengah halaman, klik → Cuti & Absensi)
- **RecentActivityWidget:** kolom Aksi (Dibuat/Diubah/Dihapus) + Menu + Aktivitas (readable), filter Menu dinamis + User + Rentang Waktu, pagination

Layout: Stats (full) → [Expiring | Cuti] → RecentActivity (full)

### 2.10 Role, Access & Permission Management (Spatie Permission)

#### 2.10.1 Default Role-Based Access

| Role | Hak |
|------|-----|
| **Admin** | Full — semua menu, semua data, CRUD user, delete semua (bypass implicit via `Gate::before`) |
| **Checker Retur** | Retur Masuk Cabang, Retur Keluar Supplier — data sendiri |
| **Checker Terima** | Datang Mobil, Terima Supplier, Input SJ, Komplain PO — data sendiri |
| **Checker Keluar** | Keluar Barang, Input Kirim Barang — data sendiri |
| **Checker Kiriman** | Kiriman Mobil — data sendiri |

Default permission per role di-set di `PermissionSeeder::assignRoleDefaults()` (Admin = 84, Terima = 19, Retur/Keluar = 11, Kiriman = 7).

#### 2.10.2 Fine-Grained Permission (Per-Menu & Per-Action)

Di atas role default, Admin bisa kustomisasi akses **per-user** melalui UI Edit User:

- **Permission names:** `{action}_{module_key}` — contoh: `view_task_retur_cabangs`, `create_task_retur_cabangs`, `update_task_retur_cabangs`, `delete_task_retur_cabangs`
- **4 actions per module:** view, create, update, delete
- **Total 85 permission** (20 modul × 4 = 80 + 4 widget `view_widget_*` + `view_all_data`)
- **Coverage:** Semua modul task, master data, non-task (Input SJ, BranchShipment, Retur Masuk/Keluar, Pusat Dokumen, Cuti & Absensi, Users, TvBoard, KendaraanDokumen)

#### 2.10.3 UI Akses Menu (Modal User — Tabs)

Create/Edit User & Role kini **modal** (halaman `/create`, `/{id}/edit` dihapus). Form pakai **Tabs**:

- **User:** Tab *Informasi* (Name|Email|Password|Role, 4 kolom sejajar) → Tab *Akses Menu & Fitur* → Tab *Dashboard & Widgets*
- **Role:** Tab *Informasi* (Nama + Super Admin) → Tab *Detail Template*
- `Tabs::make()->columnSpanFull()` — wajib agar isi melebar penuh (resolver modal memaksa `columns(2)`)

Tree akses (via `PermissionMenu`): **Akses Global** (`view_all_data`) → Group (collapsible) → per modul `Fieldset` 5 kolom = **Pilih Semua** + View + Create + Update + Delete → widget "Aktif".

- State: field `perm_{permission}` → `syncPermissions()` ke Spatie `model_has_permissions` — sekarang dieksekusi di `CreateAction::using()` / `EditAction::using()` (bukan `afterCreate`/`afterSave`)
- Checkbox `perm_*` **dehydrated** (ikut payload `$data`); `select_all_*` tetap UI-only
- Load state saat edit: `getDirectPermissions()` (User) / `permission_template` (Role); role Select `live()` → pre-fill dari template
- **Admin bypass:** Admin tetap punya akses penuh implicit (Spatie gate `before`)

#### 2.10.4 Resource Authorization Pattern

```php
canViewAny()     → auth()->user()->can('view_{module}')
canCreate()      → auth()->user()->can('create_{module}')
canEdit()        → auth()->user()->can('update_{module}')
canDelete()      → auth()->user()->can('delete_{module}')
getEloquentQuery() → filter own data untuk non-Admin (via permission check)
```

### 2.11 UI/UX
- **Primary color:** `#EA580C` (orange)
- **Compact table:** `py-2px` cells, `striped` rows, table borders
- **Sidebar:** collapsible, groups collapsed by default, persist via localStorage
- **Icons:** Semua tombol Create pake `->icon('heroicon-m-plus')`
- **Font:** Arial 14px
- **Widget "Datang Mobil"** (dashboard): value `{total}/{status SELESAI}` → **`43/40`**, angka selesai **hijau** (`aurum-stat-value--green`); override blade stats-overview render value sebagai HTML-safe (`{!! !!}`)

### 2.12 Filter UI Standard (AboveContent)

6 menu task sudah menggunakan layout `AboveContent` — filter langsung terlihat di atas tabel tanpa klik dropdown:

| Menu | Jumlah Filter | Layout |
|------|--------------|--------|
| Input Kirim Barang | 4 (Kiriman, Cabang, Tanggal, Status) | 5 kolom |
| Checker Keluar Barang | 3 (Cabang, Tanggal, Status) | 3 kolom |
| Kiriman Mobil | 4 (Cabang, Tgl Kirim, Retur, Status) | 4 kolom |
| Checker Terima Supplier | 3 (Supplier, Status, Tanggal) | 4 kolom |
| Datang Mobil Supplier | 5 (Supplier, Ekspedisi, Jenis Kiriman, Tgl Datang, Status) | 5 kolom |
| Input SJ Supplier | 3 (Nama Supplier, Tgl Input, Status) | 3 kolom |
| Retur Masuk dari Toko | 4 (Toko, Jenis Retur, Status, Tanggal) | 4 kolom |
| Retur In & Out Supplier | 3 (Supplier, Jenis, Tanggal Buat) | 3 kolom |

**Aturan:**
- `SelectFilter` → auto-apply saat dipilih
- `Filter` + DatePicker → ada tombol Terapkan
- Date inline: `Grid::make(2)` untuk Dari + Sampai
- Helper text: lowercase ("tgl mulai", "tgl akhir")
- Semua kolom tabel: `->toggleable()` untuk show/hide kolom

### 2.13 Aturan KIR (Masa Berlaku STNK/KIR)
- **KIR hanya tampil jika `masa_berlaku_kir` terisi** (grid Masa Berlaku & Master Kendaraan)
- **Motor** → tidak ada KIR (tidak perpanjang KIR) — field KIR di form tersembunyi, tidak auto-create
- **Mobil pribadi** (tidak isi `masa_berlaku_kir`) → KIR tersembunyi
- **Mobil isi `masa_berlaku_kir`** → KIR otomatis muncul di grid Masa Berlaku

### 2.14 User & Role Management (Modal)
- Create/Edit User & Role dilakukan lewat **modal** (bukan halaman terpisah) — `getPages()` hanya `index`
- Form ber-**Tabs**: Informasi + Akses/Hak Akses (dan Dashboard/Widget untuk User)
- Lebar modal `Width::Full`, tanpa tombol "Buat & buat lainnya"
- Analog rule tetap: hanya Super Admin yang mengelola Users & Roles

### 2.15 Activity Log (Aktivitas Terakhir) — Otomatis + Mudah Dibaca
- Log **create / update / delete** otomatis di **semua menu** lewat trait `LogsActivity` (activity `ActivityLogger`)
  - 6 task: Retur Cabang, Retur Supplier, Datang Mobil, Terima Supplier, Keluar Barang, Kiriman Mobil
  - Non-task: Input Kirim (BranchShipment), Input SJ, Komplain PO, Pusat Dokumen, Masa Berlaku STNK/KIR, Cuti & Absensi, Master data, User, Role
- Deskripsi memakai **label Bahasa Indonesia** (mis. "Qty Check: 10 → 15"), modul di-sync dengan **label menu**
- Widget **Aktivitas Terakhir**: kolom **Aksi** (badge hijau Dibuat / kuning Diubah / merah Dihapus + ikon), **Menu** (badge+ikon), **Aktivitas** (wrap, rapikan data lama), ID, Referensi, User, Waktu; filter **Menu** (dinamis dari data) + **User** + **Rentang Waktu**

### 2.16 Status Online User
- Kolom **Status** di menu Users: badge **hijau "Online"** (wifi) jika user aktif dalam 10 menit terakhir, badge **abu "Offline"** (signal-slash) sebaliknya
- Deteksi berbasis **cache** (`user-online-{id}`, 10 menit) lewat middleware `TrackLastActive` — tanpa kolom database baru

### 2.17 Polish
- Header grup tabel Masa Berlaku STNK/KIR dibersihkan dari mojibake emoji + dibuat **bold**
- Tab & gate "Atur Saldo Cuti" di `ManageLeaves` di-protect oleh permission `update_cuti_absensi`
- Komplain PO: label `Tanggal Datang Barang` → **`Tanggal Penyelesaian`** (form/grid/modal/export) + helper Foto Barang

### 2.18 Retur Masuk dari Toko (TaskReturCabang)
- Modal create/edit `Width::SevenExtraLarge` (seragam 75%)
- Data Retur: `Jenis Retur | Tanggal Bongkar | Jam Bongkar` 1 baris; **Helper** full-width
- Jumlah SJ + Catatan dalam Fieldset **Retur Bagus / Retur Jelek** (berdampingan saat `rb_dan_rj`; `Jumlah SJ | Catatan` sejajar)
- Simpan **`kiriman_mobil_id`** (FK): dropdown kiriman **mengecualikan yang sudah dipakai** (`whereDoesntHave`); edit include record-nya
- `no_plat_mobil`/`jam_tiba`/`nama_sopir` nullable + fallback `nama_supir ?? ''` (tahan kiriman tanpa sopir/plat)
- **`Tanggal Bongkar`**: `maxDate(now())` (picker tolak tanggal masa depan) + rule `before_or_equal:today` (create & edit)

### 2.19 Retur In & Out Supplier (SupplierReturn)
- Modal `Width::SevenExtraLarge`
- Data Supplier: `Pilih Mobil | Jenis Pengiriman*` (baris 1); autofill `Supplier | Ekspedisi | Supir / Plat | Tgl | Jam` (2×3)
- `Jenis Pengiriman*` pindah ke **Detail Retur**; baris `No Nota | Status`; FieldSet **Retur Keluar / Retur Masuk** kondisional
- **`Potong Nota`** milik **Retur Keluar**; Retur Masuk = Servis / Ganti Barang
- Retur create/update/delete → sinkron status truk (syncStatus)
- Truk **SELESAI** bila semua kewajiban beres sesuai `jenis_kiriman` (DATANG→terima selesai; RETUR→retur selesai; DATANG & RETUR→**keduanya**); retur masih draft/belum → truk PROSES
- **Filter Supplier** (searchable, dari `nama_supplier` distinct) di posisi **sebelum Jenis**; `filtersFormColumns(3)` → 1 baris `Supplier | Jenis | Tanggal Buat`

### 2.20 Datang Mobil Supplier — Status Otomatis
- Field `status` dihapus dari form; baru = `MENGANTRI`
- Modal `Width::SevenExtraLarge`
- `syncStatus()` (retur & terima): truk SELESAI bila syarat per `jenis_kiriman` terpenuhi; `jam_selesai` dari waktu terlambat
- Guard hapus truk untuk Terima **dan** Retur

### 2.21 Input SJ dari Supplier — Lifecycle Otomatis
- Data otomatis dari `TaskTerimaSupplier` (SELESAI); **tidak ada tombol Tambah** & delete manual (**DeleteBulk dihapus**)
- Terima jadi draft / Terima dihapus → **SJ terkait ikut terhapus**
- Edit SJ: `Selesai` wajib No PO (error di field, modal tetap terbuka); `tempo_hari` auto-hitung dari `tanggal_input − tanggal_datang`

---

## 3. Database

### 6 Task Tables
`task_retur_cabangs | task_retur_suppliers | arrival_supplier_trucks | task_terima_suppliers | task_keluar_barangs | task_kiriman_mobils`
Semua punya: `id_task` (indexed), `user_id` (FK).

**task_keluar_barangs:** `id_task, branch_shipment_id (FK), cabang, nomor_sj, total_qty, no_po, jam_disiapkan, diserahkan_kepada, helper (JSON), status (draft/siap kirim/selesai), keterangan, user_id`

**task_kiriman_mobils:** `id_task, cabang, no_plat_mobil (nullable), jam_muat (nullable), jam_selesai_muat (nullable), jam_berangkat (nullable), jam_tiba (nullable), tanggal_kirim (nullable), nama_supir (nullable), status (draft/dalam pengiriman/selesai), retur_option (tidak_ada_retur/ada_retur), keterangan, keluar_barang_id (FK nullable), user_id`

### 7 Master Tables
`expeditions | master_kendaraans | master_sopirs | master_tokos | suppliers | warehouse_employees | divisions`

### 6 Non-Task Tables
`supplier_sjs | branch_shipments | supplier_return_inbounds | branch_return_outbounds | warehouse_leaves | activity_logs`

### Support Tables
`task_terima_supplier_helpers` (pivot)
`task_id_counters` (global counter ID_TASK)
`branch_shipment_kiriman_mobil` (pivot: task_kiriman_mobils ↔ branch_shipments)

---

## 4. UI Navigation

| Grup | Menu |
|------|------|
| (dashboard) | Dasbor |
| Master (Admin) | Ekspedisi, Kendaraan, Sopir, Toko, Supplier, Employee Gudang |
| Purchasing Order | Komplain PO |
| Retur | Retur Masuk Cabang, Retur In & Out Supplier |
| Penerimaan | Input SJ Supplier, Datang Mobil Supplier, Checker Terima Barang Supplier |
| Pengiriman | Input Kirim Barang, Checker Keluar Barang, Kiriman Mobil |
| Administrasi (Admin) | Cuti & Absensi, **Pusat Dokumen** |
| Pengaturan (Admin) | Users |
| (toolbar) | Export XLSX/PDF (custom service) |

### 2.11 Komplain PO
- **Grup:** Purchasing Order | **Icon:** DocumentText | **Akses:** RBAC `komplain_pos` (default Admin + Checker Terima)
- **Tabel:** `po_complaints` | **ID Task:** `KMPL-00001` (TaskIdGenerator)
- **Tujuan:** Mencatat komplain barang tidak lengkap/tidak sesuai
- **Form 3 Section:** PO Supplier (cabang, supplier, no_po, barcode) → Barang (nama, qty diterima, no surat jalan, qty di SJ, foto max 5, tgl datang) → Status (kondisi, penyelesaian, status, keterangan)
- **Status:** `draft` (default) / `selesai` — status Selesai hanya bisa jika `tanggal_datang_barang` terisi
- **Foto:** min 1, max 5, disk public `fotos-komplain/`, view di modal detail (ImageEntry), tooltip nama file di grid
- **Kondisi:** `tidak_sesuai` / `tidak_lengkap`
- **Penyelesaian:** `potong_nota` / `retur` / `ganti_barang`
- **Export:** XLSX + PDF (custom TableExportService)

---

## 5. Data Integrity & Protection

### 5.1 Cascade Hapus
- **TaskTerimaSupplier dihapus** → `deleted` event revert `ArrivalSupplierTruck.status` ke `PROSES`, `jam_selesai` ke `null`
- **ArrivalSupplierTruck dihapus** → `deleting` event cek apakah terikat `TaskTerimaSupplier`. Jika iya, **cegah hapus** dengan `ValidationException`

### 5.2 Edit Mode Protection
- `arrival_supplier_truck_id` dropdown di **disable** saat Edit — tidak bisa ganti mobil
- `branch_shipment_id` dropdown di **disable** saat Edit — tidak bisa ganti kiriman
- Dropdown options: include record yang sedang diedit (via `->options()` closure) agar validasi tidak gagal

### 5.3 Helpers Display
- Grid helpers: max **2 nama** + `+N more` badge hijau
- Tampil compact 1 baris (tidak melebar vertikal)
- **Tooltip:** hover badge helper → muncul semua nama dipisah koma

---

## 6. UI Modal Standards

### ViewAction Detail Modal
Semua modul menggunakan **tampilan seragam**:
- `Section::make('Informasi Task')->columns(2)` — layout 2 kolom rapi
- `->modalSubmitAction(false)` — hapus tombol submit
- `->modalCancelAction(fn => label('Tutup'))` — tombol tutup
- Badge warna untuk status dan jenis

### Edit/Create Modal
- Form dipisah jadi **beberapa Section logis** (2-4 Section per form)
- Contoh: Kiriman Mobil → Data Kiriman, Waktu Perjalanan (3 kolom), Kendaraan & Sopir, Status & Catatan
- `columns(2)` sebagai default, `columns(3)` untuk grup waktu/jadwal
- `columnSpanFull()` untuk field penting (Pilih SJ, nomor_sj, keterangan)
- Modal width: **`Width::SevenExtraLarge`** (≈75%) sebagai standar form kompleks (Retur Masuk/Suple, Retur In & Out, Datang Mobil; `Width::Full` hanya untuk kelola User/Role/Pusat Dokumen)
- Field disabled: autofill dari relasi (disimpan ke DB via dehydrated)
- Select: `->searchable()->preload()` untuk UX cepat
- Helper: `->badge()->separator(', ')` dengan state return array
- **Toggleable columns:** Semua kolom tabel bisa di-show/hide via tombol Columns
- **Auto-set status:** jam_berangkat diisi → status otomatis "Dalam Pengiriman"

### Single Form (No Repeater)
Semua modul input **satu per satu** — tidak ada Repeater multi-row.

### UI/UX Form Standards (v2.2)
- Form tidak "pukul rata" — tiap Section punya kolom sendiri (2/3)
- Field penting (Pilih SJ, nomor_sj, keterangan) pakai `columnSpanFull()`
- Setiap Section punya judul + deskripsi kontekstual
- Data referensi (auto-fill) dipisah dari input manual
- Grup waktu/jadwal pakai `columns(3)` agar lebih rapat
- Kolom tabel bisa toggle show/hide via tombol Columns
- Tombol "Input SJ Baru" dihapus — SJ auto-create dari Terima Supplier
- **Header actions:** semua tombol Tambah/Simpan hijau solid, ukuran kecil (CSS)
- **Aurum Theme:** tema Gold-on-Graphite (preset Sapphire: biru safir)
- **Helpers badge:** warna orange (warning) + tooltip semua nama

---

## 7. Export Custom — XLSX & PDF

Export langsung tanpa plugin — 2 tombol **outlined kotak kecil** (XLSX & PDF) di toolbar tabel, sejajar sebelum kolom search. Klik = download sesuai filter aktif.

### Service: `app/Services/TableExportService.php`
```php
// Query-based (menu resource table)
TableExportService::streamXlsx(Builder $query, array $columns, string $fileName, array $formatters = []): StreamedResponse
TableExportService::streamPdf(Builder $query, array $columns, string $fileName, array $formatters = []): StreamedResponse

// Array-based (custom page, mis. Cuti & Absensi)
TableExportService::streamXlsxFromRows(array $headers, array $rows, string $fileName): StreamedResponse  // border + header bold
TableExportService::streamPdfFromRows(array $headers, array $rows, string $fileName): StreamedResponse

TableExportService::resolveValue($record, string $path): string
```
- `columns` = `['Label' => 'kolom.path']` — dukung dot-notation relasi (`supplier.nama_supplier`)
- **`$formatters`** = `['path' => fn($record) => string]` — konversi kolom khusus (helper → nama, status → label, computed)
- XLSX: OpenSpout, chunk 500, stream ke `php://output`
- PDF: HTML table (`table-pdf.blade.php` / `table-pdf-rows.blade.php`) → dompdf, A4 landscape, limit 200
- XLSX array (Cuti): **border semua sel + header bold**

### Pemasangan di Table (`toolbarActions`)
```php
use App\Services\TableExportService;
use Filament\Support\Enums\Size;

->toolbarActions([
    Action::make('export_xlsx')
        ->label('Export XLSX')
        ->icon('heroicon-o-document-arrow-down')->color('success')
        ->outlined()->size(Size::Small)
        ->action(fn (Action $a) => TableExportService::streamXlsx(
            $a->getLivewire()->getFilteredTableQuery(),
            self::exportColumns(), 'nama-file', self::exportFormatters())),
    Action::make('export_pdf')
        ->label('Export PDF')
        ->icon('heroicon-o-document-text')->color('danger')
        ->outlined()->size(Size::Small)
        ->action(fn (Action $a) => TableExportService::streamPdf(
            $a->getLivewire()->getFilteredTableQuery(),
            self::exportColumns(), 'nama-file', self::exportFormatters())),
])
```

### Status — Menu yang Sudah Ada Export
| Menu | Formatters |
|------|-----------|
| Datang Mobil Supplier | — |
| Checker Terima Barang Supplier | — |
| Input SJ Dari Supplier | — |
| Input Kirim Barang | — |
| Checker Keluar Barang | helper → nama karyawan |
| Kiriman Mobil | total_sj, SJ list, status/retur → label |
| Cuti & Absensi (Papan Absensi matrix) | C/S/I matrix + Sisa |

### Catatan
- **Style tombol:** outlined kotak kecil — `->outlined()->size(Size::Small)` (enum `Filament\Support\Enums\Size`)
- Export menghormati filter aktif + scope role (`getFilteredTableQuery()`)
- Plugin `occtherapist/advanced-table-export-for-filament` **dibuatalkan** (UX modal tidak sesuai) — di-revert
- Dependensi: `dompdf/dompdf` (openspout via `filament/actions`)
- Border XLSX: khusus Cuti & Absensi dulu (menu lain menyusul jika diminta)

## 7. Deployment & Fix 403 di Production

### Deploy ke Hostinger
- URL: `gudang.mutiarasuperkitchen.com`
- Document root: `public_html/gudang/public/`
- PHP 8.3+, MySQL via hPanel

### Fix Kritis — 403 di Production
**Masalah:** Setelah login berhasil & session valid, semua `/admin/*` tetap 403.

**Penyebab:** Filament middleware `Authenticate` memblokir akses panel di environment `production` jika model User **tidak implement `FilamentUser`**:
```php
abort_if(
    $user instanceof FilamentUser ?
        (! $user->canAccessPanel($panel)) :
        (config('app.env') !== 'local'),
    403,
);
```

**Fix:** `app/Models/User.php`
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

### Konfigurasi Session untuk Hosting
- `SESSION_DRIVER=database` (paling reliable di shared hosting)
- `bootstrap/app.php`: `encryptCookies(except: ['jobdeskgudangap-session'])` — exclude session cookie dari enkripsi
- `bootstrap/app.php`: `trustProxies(at: '*', ...)` — trust proxy Hostinger (HTTPS → HTTP internal)

## 8. Referensi

- [Filament v5 Documentation](https://filamentphp.com/docs/5.x/)
- [Filament v5 Actions / Edit](https://filamentphp.com/docs/5.x/actions/edit)
- [Filament v5 Tables](https://filamentphp.com/docs/5.x/tables)
- [Filament v5 Forms](https://filamentphp.com/docs/5.x/forms)
- [Filament v5 Infolists](https://filamentphp.com/docs/5.x/infolists)
- [Laravel 13 Documentation](https://laravel.com/docs/13.x)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v6/)
- [Filament Hub (Plugin Repository)](https://filament-hub.com/features/4.x)
- [Advanced Table Export for Filament](https://github.com/occtherapist/advanced-table-export-for-filament)
