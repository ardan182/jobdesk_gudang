# PRD — Jobdesk Gudang AP

**Versi:** 1.2 | **Tanggal:** 17 Juli 2026

---

## 1. Ringkasan

Aplikasi pencatatan jobdesk harian gudang berbasis web. Digitalisasi log harian — retur, penerimaan, pengiriman barang — dengan multi-user role-based access via Filament v5 admin panel.

---

## 2. Fitur Utama

### 2.1 Manajemen Master Data
- **6 Master Module:** Ekspedisi, Kendaraan, Sopir (+no_whatsapp), Toko, Supplier (import XLSX/XLS/CSV), Employee Gudang (import + Divisions Tabs)
- **Divisions:** Kelola divisi via widget di Employee Gudang
- **Import:** Supplier & Employee via upload CSV/XLSX/XLS (ZipArchive, tanpa library eksternal)
- **Export Template:** Download template XLSX via route `suppliers/template`, `employees/template`

### 2.2 Modul Task (Log Harian)
| Modul | Prefix | Role |
|-------|--------|------|
| Retur ke Supplier | RET-SUP | Checker Retur |
| Retur dari Cabang | RET-CAB | Checker Retur |
| Datang Mobil Supplier | ARR-SUP | Checker Terima |
| Terima Barang Supplier | TRM-SUP | Checker Terima |
| Keluar Barang | KLR | Checker Keluar |
| Kiriman Mobil | KRM | Checker Kiriman |

Semua modul:
- **Input via Repeater modal** (tidak ada halaman Edit terpisah)
- **Batch submit:** 1 ID_TASK per submit, semua row dalam 1 form share ID yang sama
- **ID_TASK:** Auto-generate format `PREFIX-NNNNN` (global sequential counter 5 digit, per-row unique)
- **ViewAction + EditAction** via iconButton + tooltip
- **Only Admin can delete** — Checker roles hanya bisa view/create/edit own records

### 2.3 Cuti & Absensi
- Halaman `ManageLeaves` di grup **Administrasi**
- **Monthly matrix grid:** Karyawan (rows) x Tanggal (columns)
- **Filter:** Bulan, Tahun, Divisi, Hanya yang absen
- **Warna:** Cuti (merah `C`), Sakit (kuning `S`), Izin (biru `I`)
- **Hapus:** klik badge langsung hapus
- **Input:** Modal form dengan pilih karyawan, tanggal, tipe, keterangan
- **Validasi:**
  - `minDate(today)` — tidak bisa backdate
  - No duplicate date per karyawan
  - Max 12 Cuti per tahun (reset tahunan)
  - Sakit & Izin unlimited
- **Sisa Cuti:** Kolom sticky kanan, hijau jika >0, merah jika 0
- **Striped rows**, sticky nama karyawan (kiri), sticky sisa cuti (kanan)

### 2.4 Dashboard
- **StatsOverviewWidget:** 6 card (5 task + admin user count) — real-time per role
- **RecentActivityWidget:** 10 log terakhir, deskripsi `wrap` (tanpa truncate)

### 2.5 Role & Akses (Spatie Permission)
| Role | Hak |
|------|-----|
| **Admin** | Full access — lihat semua data semua user, CRUD user, delete semua record |
| **Checker Retur** | Retur Supplier + Retur Cabang — hanya melihat/edit data sendiri |
| **Checker Terima** | Datang Mobil + Terima Supplier — hanya melihat/edit data sendiri |
| **Checker Keluar** | Keluar Barang — hanya melihat/edit data sendiri |
| **Checker Kiriman** | Kiriman Mobil — hanya melihat/edit data sendiri |

### 2.6 UI/UX
- **Navigation groups collapsed by default** (kecuali group aktif) via Alpine + localStorage
- **Compact table:** `py-0.125rem` cell, `striped` rows
- **Color-coded actions:** `->color()` pada semua tombol
- **Column width:** `->grow(false)` + `->width()` di semua 11 tabel
- **Sidebar:** Collapsible + persist state

---

## 3. Alur Data

### ID_TASK Generation
- File: `app/Services/TaskIdGenerator.php`
- Format: `{PREFIX}-{5 digit global counter}` — contoh `RET-SUP-00001`
- Counter tabel `task_id_counters` — increment + lock per transaksi
- 1 row = 1 ID_TASK (tidak lagi batch share ID)

### Batch Insert Flow
1. User isi repeater → klik Simpan
2. System loop setiap row → panggil `TaskIdGenerator::generate()` per row
3. Insert semua row ke DB → flash notifikasi sukses

---

## 4. Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 13 |
| Admin Panel | Filament v5 |
| Database | MySQL / MariaDB |
| Auth | Spatie Laravel Permission |
| Frontend | Tailwind CSS + Alpine.js (Filament bundled) |
| Export/Import | ZipArchive (native PHP, no external lib) |

### Constraints Teknis
- **PHP 8.5.8** — terlalu baru untuk `maatwebsite/excel` dan `phpoffice/phpspreadsheet` (require PHP <8.5). Semua XLSX via ZipArchive + SimpleXML.
- **Laravel 13** — storage disk `local` root di `storage/app/private/`
- **Livewire FileUpload** — file import disimpan permanen di `storage/app/private/imports/`

---

## 5. Struktur Database

### Task Tables (6)
`task_retur_suppliers | task_retur_cabangs | task_datang_mobil_suppliers | task_terima_suppliers | task_keluar_barangs | task_kiriman_mobils`

Semua punya: `id_task` (indexed, not unique), `user_id` (FK), timestamps.

### Master Tables
`expeditions | master_kendaraans | master_sopirs | master_tokos | suppliers | warehouse_employees | divisions`

### Leave Table
`warehouse_leaves` — `warehouse_employee_id | tanggal | tipe (Cuti|Sakit|Izin) | keterangan | user_id`

### Support Tables
`task_id_counters` — global counter untuk ID_TASK
---

## 6. Role Access Matrix

| Menu | Admin | Retur | Terima | Keluar | Kiriman |
|------|-------|-------|--------|--------|---------|
| Dashboard | ✓ | ✓ | ✓ | ✓ | ✓ |
| Master Ekspedisi | ✓ | - | - | - | - |
| Master Kendaraan | ✓ | - | - | - | - |
| Master Sopir | ✓ | - | - | - | - |
| Master Toko | ✓ | - | - | - | - |
| Master Supplier | ✓ | - | - | - | - |
| Master Employee Gudang | ✓ | - | - | - | - |
| Retur Supplier | ✓ | ✓ | - | - | - |
| Retur Cabang | ✓ | ✓ | - | - | - |
| Datang Mobil Supplier | ✓ | - | ✓ | - | - |
| Terima Supplier | ✓ | - | ✓ | - | - |
| Keluar Barang | ✓ | - | - | ✓ | - |
| Kiriman Mobil | ✓ | - | - | - | ✓ |
| Cuti & Absensi | ✓ | - | - | - | - |
| Users | ✓ | - | - | - | - |
