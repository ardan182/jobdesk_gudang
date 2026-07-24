# PRD — Jobdesk Gudang AP

**Versi:** 2.1 | **Tanggal:** 24 Juli 2026

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
- **StatsOverviewWidget:** 5 card (Admin) atau sesuai role
- **RecentActivityWidget:** 10 log terakhir, filter module, pagination

### 2.10 Role & Access (Spatie Permission)

| Role | Hak |
|------|-----|
| **Admin** | Full — semua menu, semua data, CRUD user, delete semua |
| **Checker Retur** | Retur Masuk Cabang, Retur Keluar Supplier — data sendiri |
| **Checker Terima** | Datang Mobil, Terima Supplier — data sendiri |
| **Checker Keluar** | Keluar Barang — data sendiri |
| **Checker Kiriman** | Kiriman Mobil — data sendiri |

### 2.11 UI/UX
- **Primary color:** `#EA580C` (orange)
- **Compact table:** `py-2px` cells, `striped` rows, table borders
- **Sidebar:** collapsible, groups collapsed by default, persist via localStorage
- **Icons:** Semua tombol Create pake `->icon('heroicon-m-plus')`
- **Font:** Arial 14px

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
| Retur | Retur Masuk Cabang, Retur Keluar Supplier, Retur Masuk Supplier, Retur Keluar Cabang |
| Penerimaan | Input SJ Supplier, Datang Mobil Supplier, Checker Terima Barang Supplier |
| Pengiriman | Input Kirim Barang, Checker Keluar Barang, Kiriman Mobil |
| Administrasi (Admin) | Cuti & Absensi, **Pusat Dokumen** |
| Pengaturan (Admin) | Users |

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
- Form dalam `Section` + `columns(2)` untuk form auto-fill
- Modal width: `Width::Full` untuk form kompleks
- Field disabled: autofill dari relasi (disimpan ke DB via dehydrated)
- Select: `->searchable()->preload()` untuk UX cepat
- Helper: `->badge()->separator(', ')` dengan state return array
- **Toggleable columns:** Semua kolom tabel bisa di-show/hide via tombol Columns
- **Auto-set status:** jam_berangkat diisi → status otomatis "Dalam Pengiriman"

### Single Form (No Repeater)
Semua modul input **satu per satu** — tidak ada Repeater multi-row.

---

## 7. Referensi

- [Filament v5 Documentation](https://filamentphp.com/docs/5.x/)
- [Filament v5 Actions / Edit](https://filamentphp.com/docs/5.x/actions/edit)
- [Filament v5 Tables](https://filamentphp.com/docs/5.x/tables)
- [Filament v5 Forms](https://filamentphp.com/docs/5.x/forms)
- [Filament v5 Infolists](https://filamentphp.com/docs/5.x/infolists)
- [Laravel 13 Documentation](https://laravel.com/docs/13.x)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v6/)
- [Filament Hub (Plugin Repository)](https://filament-hub.com/features/4.x)
