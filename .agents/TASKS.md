# Task List: Jobdesk Gudang AP

## Ringkasan

| Total Task | Priority High | Priority Mid | Priority Low |
|------------|--------------|--------------|--------------|
| 29 | 13 | 9 | 7 |

## Legend Prioritas & Status
- **High:** Wajib dikerjakan (Must Have V1)
- **Mid:** Penting tapi bisa menyusul
- **Low:** Nice to have

---

## T-01: Setup Project Laravel + Filament

- **Modul:** — Setup
- **Prioritas:** High
- **Status:** Todo
- **Dependensi:** —

**Deskripsi:**
Install Laravel 13 baru + Filament v5 + Spatie Permission + konfigurasi database MySQL.

**Acceptance Criteria:**
- [x] `composer create-project laravel/laravel jobdeskgudang`
- [x] `composer require filament/filament:"^3.2" -W`
- [x] `php artisan filament:install --panels`
- [x] `composer require spatie/laravel-permission`
- [x] `php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`
- [x] Konfigurasi `.env` database MySQL
- [x] `php artisan migrate` berhasil
- [x] Seeder auto-create admin user (`admin@jobdesk.test` / `password`) + assign role Admin
- [x] Bisa akses `/admin` dan login

**Files:**
- `composer.json`
- `.env`
- `config/filament.php`
- `config/permission.php`

---

## T-02: Role & User Management

- **Modul:** — Auth
- **Prioritas:** High
- **Status:** Todo
- **Dependensi:** T-01

**Deskripsi:**
Buat role (Admin, Checker Retur, Checker Terima, Checker Keluar, Checker Kiriman), seed default, dan Filament UserResource dengan role assignment.

**Acceptance Criteria:**
- [x] Seeder: `RoleSeeder` dengan 5 role (`Role::firstOrCreate` — idempotent)
- [x] Seeder: auto-create user Admin + assign role (`User::firstOrCreate`)
- [x] Filament `UserResource` bisa CRUD user + pilih role
- [x] Middleware/guard: setiap resource di-protect berdasarkan role
- [x] Bisa login sebagai checker & lihat menu sesuai role

**Files:**
- `database/seeders/RoleSeeder.php`
- `database/seeders/DatabaseSeeder.php`
- `app/Filament/Resources/UserResource.php`
- `app/Providers/AppServiceProvider.php`

---

## T-03: Task ID Generator Service (Done)

- **Modul:** — Core
- **Prioritas:** High
- **Status:** Done
- **Dependensi:** T-01

**Deskripsi:**
Buat service `TaskIdGenerator` yang generate `ID_TASK` format `{PREFIX}-{YYYYMMDD}-{XXX}` dan `NO_BARIS` auto-increment per hari per tipe task.

**Acceptance Criteria:**
- [x] Service method: `generate($type)` → return `ID_TASK` string
- [x] Service method: `getNextBaris($type)` → return integer (per hari)
- [x] Service method: `getLastIdNumber($type)` → return last counter (untuk batch next)
- [x] Service method: `formatId($type, $number)` → return formatted `ID_TASK` string
- [x] Counter per hari (reset setiap 00:00) — pakai `whereBetween` UTC karena timezone Asia/Jakarta
- [x] Prefix mapping: RET-SUP, RET-CAB, TRM-SUP, KLR, KRM
- [x] `id_task` INDEX (bukan UNIQUE) — 1 ID_TASK per batch submit, semua baris dalam 1 input ID-nya sama

**Files:**
- `app/Services/TaskIdGenerator.php`

---

## T-04: Modul Checker Retur — Kirim Retur to Supplier (Done)

- **Modul:** Checker Retur
- **Prioritas:** High
- **Status:** Done
- **Dependensi:** T-01, T-02, T-03

**Deskripsi:**
Model, migration, dan Filament Resource untuk task kirim retur ke supplier. Form repeater input + table laporan.

**Acceptance Criteria:**
- [x] Migration `create_task_retur_suppliers_table` (field sesuai PRD)
- [x] Model `TaskReturSupplier` dengan fillable, casts, relasi ke User
- [x] Observer/boot: auto-set `user_id`, auto-generate `id_task` & `no_baris`
- [x] Filament Resource `TaskReturSupplierResource`:
  - Form dengan Repeater (ID_TASK & NO_BARIS hidden/disabled)
  - Field: nama_supplier, no_plat_mobil, nama_sopir, jam_muat (time), jumlah_kolian, admin_sj_retur, status (dropdown: Servis/Tukar/Pot Nota), keterangan
  - Table laporan: pagination, sort, filter tanggal, search
- [x] Scope: checker hanya lihat task-nya sendiri
- [x] Guard: hanya Admin & Checker Retur yang bisa akses

**Files:**
- `database/migrations/xxxx_create_task_retur_suppliers_table.php`
- `app/Models/TaskReturSupplier.php`
- `app/Filament/Resources/TaskReturSupplierResource.php`
- `app/Filament/Resources/TaskReturSupplierResource/Pages/`
- `app/Policies/TaskReturSupplierPolicy.php`

---

## T-05: Modul Checker Retur — Terima Retur dari Cabang (Done)

- **Modul:** Checker Retur
- **Prioritas:** High
- **Status:** Done
- **Dependensi:** T-01, T-02, T-03

**Deskripsi:**
Model, migration, dan Filament Resource untuk task terima retur dari cabang.

**Acceptance Criteria:**
- [x] Migration `create_task_retur_cabangs_table`
- [x] Model `TaskReturCabang`
- [x] Filament Resource dengan Repeater
- [x] Field: cabang, jenis_retur (dropdown: Retur Jelek/Retur Bagus), no_sj_retur, total_kolian, jam_bongkar (time), nama_sopir (Select dari Master Sopir), keterangan
- [x] Table laporan, pagination, sort, filter tanggal, search
- [x] Scope per user + role guard

**Files:**
- `database/migrations/xxxx_create_task_retur_cabangs_table.php`
- `app/Models/TaskReturCabang.php`
- `app/Filament/Resources/TaskReturCabangResource.php`
- `app/Filament/Resources/TaskReturCabangResource/Pages/`
- `app/Policies/TaskReturCabangPolicy.php`

---

## T-06: Modul Checker Terima Barang dari Supplier (Done)

- **Modul:** Checker Terima Barang
- **Prioritas:** High
- **Status:** Done
- **Dependensi:** T-01, T-02, T-03

**Deskripsi:**
Model, migration, dan Filament Resource untuk task terima barang dari supplier.

**Acceptance Criteria:**
- [x] Migration `create_task_terima_suppliers_table`
- [x] Model `TaskTerimaSupplier`
- [x] Filament Resource dengan Repeater
- [x] Field: nama_supplier, no_po_referensi, jumlah_kolian, jam_bongkar (time), nama_sopir, status (dropdown: Komplit/Kurang/Lebih), keterangan
- [x] Table laporan lengkap
- [x] Scope per user + role guard

**Files:**
- `database/migrations/xxxx_create_task_terima_suppliers_table.php`
- `app/Models/TaskTerimaSupplier.php`
- `app/Filament/Resources/TaskTerimaSupplierResource.php`
- `app/Filament/Resources/TaskTerimaSupplierResource/Pages/`
- `app/Policies/TaskTerimaSupplierPolicy.php`

---

## T-07: Modul Checker Keluar Barang dari Gudang (Done)

- **Modul:** Checker Keluar Barang
- **Prioritas:** High
- **Status:** Done
- **Dependensi:** T-01, T-02, T-03

**Deskripsi:**
Model, migration, dan Filament Resource untuk task keluar barang dari gudang ke toko/cabang.

**Acceptance Criteria:**
- [x] Migration `create_task_keluar_barangs_table`
- [x] Model `TaskKeluarBarang`
- [x] Filament Resource dengan Repeater
- [x] Field: toko_tujuan (dropdown: Pusat/Ujungberung/Soreang/Majalaya/Cicaheum/Barokah), supplier, no_referensi_sj, jumlah_kolian, jam_naik (time), nama_koordinator, status (dropdown: Komplit/Kurang/Lebih), keterangan
- [x] Table laporan lengkap
- [x] Scope per user + role guard

**Files:**
- `database/migrations/xxxx_create_task_keluar_barangs_table.php`
- `app/Models/TaskKeluarBarang.php`
- `app/Filament/Resources/TaskKeluarBarangResource.php`
- `app/Filament/Resources/TaskKeluarBarangResource/Pages/`
- `app/Policies/TaskKeluarBarangPolicy.php`

---

## T-08: Modul Checker Kiriman Mobil (Done)

- **Modul:** Checker Kiriman Mobil
- **Prioritas:** High
- **Status:** Done
- **Dependensi:** T-01, T-02, T-03

**Deskripsi:**
Model, migration, dan Filament Resource untuk task kiriman cabang per mobil.

**Acceptance Criteria:**
- [x] Migration `create_task_kiriman_mobils_table`
- [x] Model `TaskKirimanMobil`
- [x] Filament Resource dengan Repeater
- [x] Field: cabang, no_plat_mobil, jam_muat, jam_selesai_muat, jam_berangkat (time picker), nama_supir (Select dari Master Sopir), keterangan
- [x] Table laporan lengkap
- [x] Scope per user + role guard

**Files:**
- `database/migrations/xxxx_create_task_kiriman_mobils_table.php`
- `app/Models/TaskKirimanMobil.php`
- `app/Filament/Resources/TaskKirimanMobilResource.php`
- `app/Filament/Resources/TaskKirimanMobilResource/Pages/`
- `app/Policies/TaskKirimanMobilPolicy.php`

---

## T-09: Dashboard Admin — Stats Overview (Done)

- **Modul:** Dashboard
- **Prioritas:** High
- **Status:** Done
- **Dependensi:** T-04, T-05, T-06, T-07, T-08

**Deskripsi:**
Buat halaman Dashboard Filament dengan widget card yang menampilkan total task per checker hari ini. Admin lihat semua, checker lihat miliknya sendiri.

**Acceptance Criteria:**
- [x] Filament Widget: `StatsOverviewWidget`
- [x] Admin: 5 card (ReturSupplier, ReturCabang, TerimaSupplier, KeluarBarang, KirimanMobil) + total baris
- [x] Checker: 1 card dengan total task hari ini
- [x] Data real-time (Livewire auto-refresh opsional)
- [x] Dashboard di-set sebagai halaman default setelah login

**Files:**
- `app/Filament/Pages/Dashboard.php`
- `app/Filament/Widgets/StatsOverviewWidget.php`

---

## T-10: Admin CRUD All Menu (Backup Input) (Done)

- **Modul:** Admin
- **Prioritas:** Mid
- **Status:** Done
- **Dependensi:** T-04, T-05, T-06, T-07, T-08

**Deskripsi:**
Admin bisa mengakses semua resource checker (create/edit/delete) untuk backup jika checker absen.

**Acceptance Criteria:**
- [x] Admin bisa buka semua menu checker di sidebar
- [x] Admin bisa create, edit, delete task di semua resource
- [x] Scope per-user di-nonaktifkan untuk Admin (Admin lihat semua)

**Files:**
- Modifikasi policy/guard di semua Resource

---

## T-11: Navigation & Sidebar Menu (Done)

- **Modul:** — UI
- **Prioritas:** High
- **Status:** Done
- **Dependensi:** T-04, T-05, T-06, T-07, T-08

**Deskripsi:**
Konfigurasi navigasi Filament agar sidebar menyesuaikan role — checker hanya lihat menu sesuai role-nya, Admin lihat semua. Grup navigasi diubah ke alur proses gudang (Retur, Penerimaan, Pengiriman, Pengaturan).

**Acceptance Criteria:**
- [x] Sidebar: Admin lihat semua menu + Users
- [x] Checker Retur lihat: Dashboard, Retur ke Supplier, Retur dari Cabang (grup **Retur**)
- [x] Checker Terima lihat: Dashboard, Terima Barang Supplier (grup **Penerimaan**)
- [x] Checker Keluar lihat: Dashboard, Keluar Barang (grup **Pengiriman**)
- [x] Checker Kiriman lihat: Dashboard, Kiriman Mobil (grup **Pengiriman**)
- [x] Grup navigasi: Retur, Penerimaan, Pengiriman, Pengaturan
- [x] Sidebar collapsible (tampilkan icon saja saat collapse)

**Files:**
- `app/Providers/Filament/AdminPanelProvider.php`

---

## T-12: ID_TASK & NO_BARIS Auto-Generate + Session-Based (Done)

- **Modul:** — Core
- **Prioritas:** High
- **Status:** Done
- **Dependensi:** T-03, T-04, T-05, T-06, T-07, T-08

**Deskripsi:**
Integrasikan `TaskIdGeneratorService` ke dalam setiap Filament Resource. `id_task` bersifat session-based: 1 session (1 submit form) = 1 `id_task` untuk semua baris. `no_baris` increment per baris dalam session. UNIQUE constraint di `id_task` dihapus, diganti INDEX biasa.

**Acceptance Criteria:**
- [x] Setiap resource panggil `TaskIdGenerator::generate()` sekali per batch → 1 `id_task` untuk semua baris
- [x] `no_baris` auto-increment per baris dalam session
- [x] Migration: drop UNIQUE, add INDEX di 5 tabel
- [x] `creating` boot event fallback untuk single-record create

**Files:**
- `app/Filament/Resources/TaskReturSupplierResource.php` (modified)
- `app/Filament/Resources/TaskReturCabangResource.php` (modified)
- `app/Filament/Resources/TaskTerimaSupplierResource.php` (modified)
- `app/Filament/Resources/TaskKeluarBarangResource.php` (modified)
- `app/Filament/Resources/TaskKirimanMobilResource.php` (modified)

---

## T-13: Laporan — Filter Tanggal Default Hari Ini (Done)

- **Modul:** — Laporan
- **Prioritas:** Mid
- **Status:** Done
- **Dependensi:** T-04, T-05, T-06, T-07, T-08

**Deskripsi:**
Setiap table laporan di resource menampilkan data hari ini secara default, dengan opsi filter rentang tanggal.

**Acceptance Criteria:**
- [x] Default filter: `created_at = today`
- [x] User bisa pilih rentang tanggal kustom
- [x] Pagination tetap jalan dengan filter

**Files:**
- Modifikasi setiap Resource Table query

---

## T-14: Seeder Data Dummy (Done)

- **Modul:** — Testing
- **Prioritas:** Low
- **Status:** Done
- **Dependensi:** T-04, T-05, T-06, T-07, T-08

**Deskripsi:**
Buat seeder data dummy untuk testing semua modul.

**Acceptance Criteria:**
- [ ] Seeder: 50+ baris data per modul
- [ ] Seeder: user dummy per role
- [ ] `php artisan db:seed` jalan tanpa error

**Files:**
- `database/seeders/DatabaseSeeder.php` (modified)
- `database/seeders/DummyTaskSeeder.php`

---

## T-15: Final Testing & Polish (Done)

- **Modul:** — Testing
- **Prioritas:** Mid
- **Status:** Done
- **Dependensi:** Semua task di atas

**Deskripsi:**
Tes semua fitur: login setiap role, CRUD task, laporan, filter, dashboard. Fix bug & polish UI.

**Acceptance Criteria:**
- [x] Login sebagai Admin: bisa akses semua menu, CRUD user, CRUD task
- [x] Login sebagai Checker Retur: hanya lihat menu retur, input + laporan berfungsi
- [x] Login sebagai Checker Terima: hanya lihat menu terima barang
- [x] Login sebagai Checker Keluar: hanya lihat menu keluar barang
- [x] Login sebagai Checker Kiriman: hanya lihat menu kiriman mobil
- [x] ID_TASK & NO_BARIS auto-generate benar
- [x] Filter tanggal & search bekerja
- [x] Dashboard card menampilkan data yang akurat
- [x] Repeater form: add baris, submit multi-row berhasil
- [x] Tidak ada error/exception

**Files:**
— (testing seluruh aplikasi)

---

## T-16: UI Theme & Layout — Branding & Kustomisasi (Done)

- **Modul:** — UI
- **Prioritas:** Mid
- **Status:** Done
- **Dependensi:** T-11

**Deskripsi:**
Kustomisasi tampilan Filament: tema warna Orange Safety, font Arial, ukuran teks 14px, sidebar collapsible, konten full width, garis pemisah sidebar, grid tabel seperti Excel.

**Acceptance Criteria:**
- [x] Warna primary: Orange Safety (`#EA580C`)
- [x] Font: Arial (system font, tanpa download Google Fonts)
- [x] Ukuran teks: `html { font-size: 14px }` — lebih kecil dari default 16px
- [x] Sidebar collapsible dengan tombol toggle (icon-only saat collapse)
- [x] Lebar sidebar: `14rem` (lebih ramping dari default `20rem`)
- [x] Konten: `max-width: full` (tidak terpusat, penuhi layar)
- [x] Garis pemisah sidebar: `rgba(128,128,128,0.15)` — transparan, cocok light/dark mode
- [x] Grid tabel: `border-collapse` + border di header cell `rgba(128,128,128,0.18)` dan data cell `rgba(128,128,128,0.10)` — efek Excel
- [x] Navigasi grup: Retur, Penerimaan, Pengiriman, Pengaturan (alur proses gudang)

**Files:**
- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Filament/Resources/*/*Resource.php` (navigationGroup)

---

## T-17: Form Modal + Dashboard Widget Aktivitas Terakhir (Done)

- **Modul:** — UI
- **Prioritas:** Mid
- **Status:** Done
- **Dependensi:** T-16

**Deskripsi:**
Ubah form input task dari halaman terpisah menjadi modal full screen di List page. Tambah widget dashboard "Aktivitas Terakhir" yang menampilkan log record terbaru dari semua modul dengan pagination.

**Acceptance Criteria:**
- [x] Form create task pindah ke modal full screen (bukan halaman terpisah)
- [x] Modal berisi Repeater table (sama seperti sebelumnya)
- [x] Tombol "Tambah Baris" di pojok kanan bawah
- [x] Animasi fade-slide saat tambah baris
- [x] Resource form() disederhanakan (hapus conditional Create page)
- [x] Widget "Aktivitas Terakhir" di dashboard (paling bawah)
- [x] UNION query dari 5 tabel task, ambil 20 record terbaru
- [x] Admin lihat semua user, checker lihat sendiri
- [x] Kolom: User, Aktivitas, Waktu
- [x] Pagination: 10 / 25 / 50 baris (default 10)
- [x] Navigasi halaman lengkap (« « 1..5 » »)
- [x] Garis vertikal di tabel (border-collapse)
- [x] Timezone: Asia/Jakarta (WIB)
- [x] Header rata kiri semua

**Files:**
- `app/Filament/Resources/*/Pages/List*Task*.php` (5 List pages — modal action)
- `app/Filament/Resources/*/Task*Resource.php` (5 Resource — form disederhanakan)
- `app/Filament/Widgets/RecentActivityWidget.php`
- `resources/views/filament/widgets/recent-activity.blade.php`
- `config/app.php` (timezone → Asia/Jakarta)

---

## T-18: Nord Theme — Arctic Color Scheme (Done)

- **Modul:** — UI/Tema
- **Prioritas:** Low
- **Status:** Done
- **Dependensi:** —

**Deskripsi:**
Install Filament Nord Theme dari fork `ardan182/filament-nord-theme` untuk tampilan arctic north color scheme (light & dark mode).

**Acceptance Criteria:**
- [x] Tambah repository fork di `composer.json`
- [x] Install `andreia/filament-nord-theme:dev-3.x`
- [x] Register `FilamentNordThemePlugin` di `AdminPanelProvider`
- [x] Buat `resources/css/filament/admin/theme.css`
- [x] `php artisan filament:assets` — publish Nord CSS
- [x] `npm install && npm run build` — kompilasi aset

**Files:**
- `composer.json`
- `app/Providers/Filament/AdminPanelProvider.php`
- `resources/css/filament/admin/theme.css`
- `package-lock.json`

---

## T-19: Master Sopir CRUD + Dropdown (Done)

- **Modul:** Master Sopir
- **Prioritas:** High
- **Status:** Done
- **Dependensi:** T-01, T-02

**Deskripsi:**
Buat menu Master Sopir untuk Admin mengelola daftar sopir. Data digunakan sebagai Select dropdown di form Kiriman Mobil dan Retur dari Cabang. Tidak perlu halaman Create/Edit terpisah — pakai modal.

**Acceptance Criteria:**
- [x] Migration `create_master_sopirs_table` (id, nama_sopir, timestamps)
- [x] Model `MasterSopir`
- [x] Filament Resource `MasterSopirResource` (Admin only)
- [x] Navigasi grup "Master" setelah Dashboard
- [x] Create via modal di List page
- [x] Edit via modal di table action
- [x] Data awal "Agus" via seeder
- [x] KirimanMobilForm: `nama_supir` jadi Select dari MasterSopir
- [x] ReturCabangForm: `nama_sopir` jadi Select dari MasterSopir
- ReturSupplierForm: tetap TextInput (sopir gonta-ganti)
- TerimaSupplierForm: tetap TextInput (sopir gonta-ganti)
- KeluarBarangForm: tidak punya field sopir

**Files:**
- `database/migrations/xxxx_create_master_sopirs_table.php`
- `app/Models/MasterSopir.php`
- `app/Filament/Resources/MasterSopirs/` (resource, pages, schemas, tables)
- `app/Filament/Resources/TaskKirimanMobils/Schemas/TaskKirimanMobilForm.php` (modified)
- `app/Filament/Resources/TaskReturCabangs/Schemas/TaskReturCabangForm.php` (modified)

---

## T-20: Master Mobil CRUD + Dropdown (Done)

- **Modul:** Master Mobil
- **Prioritas:** High
- **Status:** Done
- **Dependensi:** T-01, T-02

**Deskripsi:**
Buat menu Master Mobil untuk Admin mengelola data mobil (nama & plat). Data digunakan sebagai Select dropdown di form Kiriman Mobil.

**Acceptance Criteria:**
- [x] Migration `create_master_mobils_table` (id, nama_mobil, no_plat_mobil, timestamps)
- [x] Model `MasterMobil`
- [x] Filament Resource `MasterMobilResource` (Admin only, grup "Master")
- [x] Create via modal di List page
- [x] Edit via modal di table action
- [x] KirimanMobilForm: `no_plat_mobil` jadi Select dari MasterMobil (searchable)

**Files:**
- `database/migrations/xxxx_create_master_mobils_table.php`
- `app/Models/MasterMobil.php`
- `app/Filament/Resources/MasterMobils/` (resource, pages, schemas, tables)
- `app/Filament/Resources/TaskKirimanMobils/Schemas/TaskKirimanMobilForm.php` (modified)

---

## T-21: Master Toko CRUD + Dropdown Cabang/Toko (Done)

- **Modul:** Master Toko
- **Prioritas:** High
- **Status:** Done
- **Dependensi:** T-01, T-02

**Deskripsi:**
Buat menu Master Toko untuk Admin mengelola data toko/cabang (nama & alamat). Data digunakan sebagai Select dropdown di form Kiriman Mobil, Keluar Barang, dan Retur Cabang.

**Acceptance Criteria:**
- [x] Migration `create_master_tokos_table` (id, nama_toko, alamat, timestamps)
- [x] Seeder 6 toko awal: Pusat, Ujungberung, Soreang, Majalaya, Cicaheum, Barokah
- [x] Alter ENUM `toko_tujuan` ke VARCHAR di `task_keluar_barangs`
- [x] Model `MasterToko`
- [x] Filament Resource `MasterTokoResource` (Admin only, grup "Master")
- [x] KirimanMobilForm: `cabang` → Select dari MasterToko
- [x] KeluarBarangForm: `toko_tujuan` → Select dari MasterToko
- [x] ReturCabangForm: `cabang` → Select dari MasterToko
- [x] ReturSupplier & TerimaSupplier: tidak diubah (tidak ada field cabang)

**Files:**
- `database/migrations/xxxx_create_master_tokos_table.php`
- `database/migrations/xxxx_alter_toko_tujuan_to_varchar.php`
- `app/Models/MasterToko.php`
- `app/Filament/Resources/MasterTokos/` (resource, pages, schemas, tables)
- `app/Filament/Resources/TaskKirimanMobils/Schemas/TaskKirimanMobilForm.php` (modified)
- `app/Filament/Resources/TaskKeluarBarangs/Schemas/TaskKeluarBarangForm.php` (modified)
- `app/Filament/Resources/TaskReturCabangs/Schemas/TaskReturCabangForm.php` (modified)

---

## T-22: Dashboard Stats Total + Pagination Default 25 (Done)

- **Modul:** — UI/UX
- **Prioritas:** Mid
- **Status:** Done
- **Dependensi:** T-09

**Deskripsi:**
Dashboard stats card menampilkan total seluruh data (bukan hanya hari ini). Pagination default semua tabel diubah dari 10 ke 25 baris per halaman.

**Acceptance Criteria:**
- [x] StatsOverviewWidget: hapus filter `whereBetween` → tampilkan total count
- [x] Label checker: ganti "Hari Ini" → "Total"
- [x] AppServiceProvider: `Table::configureUsing` → `defaultPaginationPageOption(25)`
- [x] MasterMobilsTable: fallback direct `defaultPaginationPageOption(25)`

**Files:**
- `app/Filament/Widgets/StatsOverviewWidget.php`
- `app/Providers/AppServiceProvider.php`
- `app/Filament/Resources/MasterMobils/Tables/MasterMobilsTable.php`

---

## T-23: Activity Log — Data Widgets untuk Dashboard (Done)

- **Modul:** Dashboard
- **Prioritas:** Mid
- **Status:** Done
- **Dependensi:** T-09, T-15

**Deskripsi:**
Buat tabel `activity_logs` untuk mencatat setiap input task. Widget dashboard "Aktivitas Terakhir" diubah dari custom blade ke Filament TableWidget bawaan.

**Acceptance Criteria:**
- [x] Migration `create_activity_logs_table` (id, user_id, module, id_task, description, action, timestamps)
- [x] Model `ActivityLog` dengan relasi ke User
- [x] 5 task models: tambah `created` event → auto-log ke activity_logs
- [x] Seeder `ActivityLogSeeder` — migrasi data task lama ke activity_logs
- [x] `RecentActivityWidget` → extends `TableWidget` (Filament built-in)
- [x] Kolom: User, Aktivitas (description), Modul (badge), Waktu
- [x] Filter by Modul (SelectFilter)
- [x] Pagination 10/25/50, default sort created_at desc
- [x] View blade lama dihapus

**Files:**
- `database/migrations/xxxx_create_activity_logs_table.php`
- `app/Models/ActivityLog.php`
- `app/Models/Task*Supplier.php` (5 models — modified)
- `app/Filament/Widgets/RecentActivityWidget.php` (rewrite)
- `database/seeders/ActivityLogSeeder.php`

---

## T-24: Bersihkan Dashboard — Hapus Widget Bawaan (Done)

- **Modul:** Dashboard
- **Prioritas:** Low
- **Status:** Done
- **Dependensi:** T-23

**Deskripsi:**
Hapus AccountWidget (Avatar + Welcome) dan FilamentInfoWidget (v5.6.8 + Documentation link) dari dashboard agar tampilan lebih bersih.

**Acceptance Criteria:**
- [x] Hapus `AccountWidget::class` dari panel widgets
- [x] Hapus `FilamentInfoWidget::class` dari panel widgets
- [x] Hapus import yang tidak dipakai

**Files:**
- `app/Providers/Filament/AdminPanelProvider.php`

---

## T-25: Kolom Refferensi di Activity Log Widget (Done)

- **Modul:** Dashboard
- **Prioritas:** Mid
- **Status:** Done
- **Dependensi:** T-23

**Deskripsi:**
Tambah kolom `reference` di tabel activity_logs dan widget dashboard. Menampilkan nomor referensi dari masing-masing task (no_plat_mobil, no_sj_retur, no_po_referensi, no_referensi_sj, nama_supir).

**Acceptance Criteria:**
- [x] Migration `add_reference_to_activity_logs` — kolom varchar nullable
- [x] 5 task models: tambah `reference` di `created` event
- [x] ActivityLog: tambah `reference` ke `$fillable`
- [x] ActivityLogSeeder: mapping reference untuk data lama
- [x] RecentActivityWidget: kolom Refferensi setelah Aktivitas

**Files:**
- `database/migrations/xxxx_add_reference_to_activity_logs.php`
- `app/Models/ActivityLog.php`
- `app/Models/TaskReturSupplier.php`
- `app/Models/TaskReturCabang.php`
- `app/Models/TaskTerimaSupplier.php`
- `app/Models/TaskKeluarBarang.php`
- `app/Models/TaskKirimanMobil.php`
- `database/seeders/ActivityLogSeeder.php`
- `app/Filament/Widgets/RecentActivityWidget.php`

---

## T-26: Checker Retur — 2 Card Dashboard (Done)

- **Modul:** Dashboard
- **Prioritas:** Low
- **Status:** Done
- **Dependensi:** T-09

**Deskripsi:**
Ubah dashboard Checker Retur dari 1 card gabungan menjadi 2 card terpisah (Retur ke Supplier & Retur dari Cabang).

**Acceptance Criteria:**
- [x] Checker Retur lihat: card "Retur ke Supplier" + card "Retur dari Cabang"
- [x] Checker lain tetap 1 card sesuai modulnya

**Files:**
- `app/Filament/Widgets/StatsOverviewWidget.php`

---

## T-27: Master Employee Gudang (Done)

- **Modul:** Master Employee Gudang
- **Prioritas:** High
- **Status:** Done
- **Dependensi:** T-01, T-02

**Deskripsi:**
Buat menu Master Employee Gudang untuk Admin mengelola data karyawan gudang (nama, no wa, divisi). Data untuk keperluan operasional tim lapangan.

**Acceptance Criteria:**
- [x] Migration `create_warehouse_employees_table` (id, nama_karyawan, no_whatsapp, divisi_gudang, timestamps)
- [x] Model `WarehouseEmployee`
- [x] Filament Resource `WarehouseEmployeeResource` (Admin only, grup "Master")
- [x] Form: nama_karyawan (required), no_whatsapp (tel, nullable), divisi_gudang (Select)
- [x] Table: nama_karyawan (searchable), no_whatsapp (icon WA + hyperlink wa.me), divisi_gudang (badge), created_at
- [x] Filter: SelectFilter divisi_gudang
- [x] Divisi: Retur, Pecah Belah, Sariindah, Elektrik, CS Gudang, Kirim Cabang, Umum
- [x] Create via modal, Edit via modal

**Files:**
- `database/migrations/xxxx_create_warehouse_employees_table.php`
- `app/Models/WarehouseEmployee.php`
- `app/Filament/Resources/WarehouseEmployees/` (resource, pages, schemas, tables)

---

## T-28: Master Sopir — No WhatsApp + View Modal + WA Link (Done)

- **Modul:** Master Sopir
- **Prioritas:** Mid
- **Status:** Done
- **Dependensi:** T-19

**Deskripsi:**
Tambah kolom `no_whatsapp` di Master Sopir. Klik baris tampilkan modal detail. No WhatsApp ada icon telepon yang klik → buka wa.me di tab baru. Tambah toggle columns.

**Acceptance Criteria:**
- [x] Migration `add_no_whatsapp_to_master_sopirs` (string 20, nullable)
- [x] Model: tambah `no_whatsapp` ke `$fillable`
- [x] Form: field `no_whatsapp` (tel), columns 2
- [x] Table: `no_whatsapp` → icon phone + url wa.me + openUrlInNewTab
- [x] `recordUrl(null)` + ViewAction modal detail
- [x] `created_at` → toggleable(isToggledHiddenByDefault: true)

**Files:**
- `database/migrations/xxxx_add_no_whatsapp_to_master_sopirs.php`
- `app/Models/MasterSopir.php`
- `app/Filament/Resources/MasterSopirs/Schemas/MasterSopirForm.php`
- `app/Filament/Resources/MasterSopirs/Tables/MasterSopirsTable.php`

---

## T-29: Master Kendaraan — Tambah Kolom + Toggle Columns (Done)

- **Modul:** Master Kendaraan
- **Prioritas:** Mid
- **Status:** Done
- **Dependensi:** T-01

**Deskripsi:**
Tambah kolom masa_berlaku_stnk, masa_berlaku_kir, keterangan di Master Kendaraan. Atur default toggle columns.

**Acceptance Criteria:**
- [x] Migration: tambah masa_berlaku_stnk (date), masa_berlaku_kir (date), keterangan (text)
- [x] Model: tambah fillable
- [x] Form: DatePicker + Textarea, columns 3
- [x] Table: 11 kolom dengan toggleable
- [x] Default muncul: nomor_polisi, jenis, merek, masa stnk, masa kir
- [x] Default hidden: rangka, mesin, stnk, kir, keterangan, created_at

**Files:**
- `database/migrations/xxxx_add_columns_to_master_kendaraans.php`
- `app/Models/MasterKendaraan.php`
- `app/Filament/Resources/MasterKendaraans/Schemas/MasterKendaraanForm.php`
- `app/Filament/Resources/MasterKendaraans/Tables/MasterKendaraansTable.php`
