# TASKS — Jobdesk Gudang AP

> Status: ✅ Selesai | 🔄 In Progress | ⏳ Planned

---

## Fase 1: Foundation ✅

- [x] Init Laravel 13 + Filament v5 + Spatie Permission
- [x] Setup panel `/admin` — `AdminPanelProvider`
- [x] 5 role: Admin, Checker Retur, Checker Terima, Checker Keluar, Checker Kiriman
- [x] TaskIdGenerator — global sequential counter (5 digit)
- [x] 6 task migration + model + resource (Retur Supplier, Retur Cabang, Terima Supplier, Keluar Barang, Kiriman Mobil, Datang Mobil)
- [x] Batch insert via Repeater pada Create & List page
- [x] Role-based access: `canDelete` hanya Admin, `getEloquentQuery` filter own data
- [x] Dashboard: StatsOverviewWidget + RecentActivityWidget

## Fase 2: Master Data ✅

- [x] Master Sopir (+no_whatsapp)
- [x] Master Toko
- [x] Master Kendaraan (rename dari MasterMobil)
- [x] Master Ekspedisi
- [x] Master Supplier + import XLSX/XLS/CSV + template download
- [x] Master Employee Gudang + Divisions Tabs + import + template download
- [x] Divisions CRUD via widget di Employee Gudang

## Fase 3: Task Modules ✅

- [x] Semua task module: modal input (no Edit page terpisah)
- [x] ViewAction + EditAction via recordAction('view'), iconButton + tooltip
- [x] Datang Mobil Supplier (ARR-SUP) — di grup Penerimaan
- [x] Single form (no Repeater) — Datang Mobil + Terima Supplier
- [x] ID_TASK refactor — global counter, no_baris dropped

## Fase 4: UI/UX ✅

- [x] Column width optimization — `grow(false)` + `width()` di semua tabel
- [x] Color-coded actions — `color('primary|warning|info|danger')`
- [x] Icons di semua tombol Create — `icon('heroicon-m-plus')`
- [x] Compact table + striped — `py-0.125rem` CSS + `striped()`
- [x] Sidebar navigation — collapsed by default via Alpine + localStorage
- [x] Sidebar persist — `collapsedGroups` disimpan
- [x] Nav group "Administrasi" ditambahkan
- [x] Brand logo + favicon (revert brand logo, favicon stay)

## Fase 5: Features ✅

- [x] Activity Log — RecentActivityWidget description `wrap` (no limit)
- [x] Export template Supplier — ZipArchive XLSX (via route)
- [x] Export template Employee — ZipArchive XLSX (via route)
- [x] SupplierImport — CSV + XLSX + XLS
- [x] WarehouseEmployeeImport — CSV + XLSX + XLS
- [x] Hapus `maatwebsite/excel` dan `phpoffice/phpspreadsheet` dari composer

## Fase 6: Cuti & Absensi ✅

- [x] Model `WarehouseLeave` + migration
- [x] Page `ManageLeaves` — monthly attendance matrix grid
- [x] Filter: Bulan, Tahun, Divisi, Hanya absen — `Section::make('Filter')`
- [x] Legend row (C/S/I colored badges)
- [x] Sticky columns: nama karyawan (kiri), sisa cuti (kanan), striped rows
- [x] CSS full width: `.fi-main{padding-inline:0!important}`
- [x] Tabs: Papan Absensi + Atur Saldo Cuti (jatah_cuti per karyawan)
- [x] Validasi: minDate (no backdate), no duplicate, max 12 cuti/tahun

## Fase 7: Modul Baru ✅

- [x] Input SJ dari Supplier — grup Penerimaan, single form
- [x] Retur Masuk dari Supplier — grup Retur, single form
- [x] Retur Keluar untuk Cabang — grup Retur, single form
- [x] Rename menu: Retur → Retur Masuk/Retur Keluar; Terima/Keluar → Checker
- [x] Datang Mobil Supplier: jenis_kiriman + status + single form
- [x] Checker Terima Supplier: FK arrival_supplier_truck + autofill + helpers pivot
- [x] Status enum: komplit/kurang/lebih → selesai_tanpa_retur/selesai_ada_retur
- [x] Graphify install + hook

## Fase 8: Bug Fixes ✅

- [x] TaskTerimaSupplier deleted → revert ArrivalSupplierTruck status ke PROSES
- [x] ArrivalSupplierTruck deleting → cegah hapus jika terikat TaskTerimaSupplier
- [x] Edit modal: arrival_supplier_truck_id validation error (ganti relationship→options)
- [x] Edit modal: dropdown mobil datang di-disable
- [x] Helpers grid: compact 2 nama + more count (green badges)
- [x] ViewAction Datang Mobil Supplier → Section 2 kolom (seragam dengan Terima Supplier)
- [x] Form Section 1 kolom untuk Datang Mobil Supplier

## Fase 9: Pusat Dokumen ✅

- [x] Model + Migration `warehouse_documents`
- [x] Resource Pusat Dokumen — grup Administrasi, icon document-arrow-down
- [x] Form: FileUpload + kategori (Formulir/SOP/Template) + versi + format auto
- [x] Grid: striped + badge kategori/format + download count
- [x] Download action: increment counter + Storage::download
- [x] Role access: Admin CRUD, all roles view + download
- [x] Format file auto-fix (pathinfo di action, bukan afterStateUpdated)
- [x] Edit modal: Width::Full, format_file auto re-extract, using() callback
- [x] FileUpload hanya required saat Create (bukan Edit)

## Fase 10: Bug Fixes & Polish ✅

- [x] Status `draft` ditambahkan (enum + form + grid badge gray)
- [x] `jumlah_kolian` dijadikan nullable (draft bisa simpan tanpa kolian)
- [x] TaskTerimaSupplier deleted → status truck sync otomatis
- [x] Cegah double select mobil datang (whereNotIn excluded IDs)
- [x] Pusat Dokumen Edit modal konsisten (Width::Full + form eksplisit)

## Fase 11: Integrasi & 3-Level Status ✅

- [x] ArrivalSupplierTruck: status MENGANTRI → PROSES → SELESAI (ENUM)
- [x] syncStatus(): auto-detect berdasarkan TaskTerima & TaskRetur
- [x] TaskTerimaSupplier: created/updated/deleted → trigger syncStatus
- [x] TaskReturSupplier: created/updated/deleted → trigger syncStatus
- [x] Badge: MENGANTRI(gray), PROSES(warning), SELESAI(success)
- [x] Filter dropdown Terima & Retur → MENGANTRI / PROSES
- [x] Retur Supplier: FK arrival_supplier_truck_id + autofill
- [x] Retur Supplier: filter RETUR & DATANG & RETUR (PROSES + SELESAI)
- [x] Status TaskTerimaSupplier: disederhanakan ke DRAFT / SELESAI
- [x] Retur Supplier trigger completion untuk DATANG & RETUR

## Fase 12: BranchShipment (Input Kirim Barang) ✅

- [x] Modul BranchShipment — grup Pengiriman, icon paper-airplane
- [x] Form: Section 2 kolom + pilih_kiriman + cabang + SJ + qty + tanggal + status
- [x] Modal width Full + ViewAction Section 2 kolom (seragam dengan modul lain)
- [x] TaskIdGenerator: prefix KRM-BRG + auto-generate id_task
- [x] Model: fillable + creating event (id_task + user_id)
- [x] Grid: id_task + badge warna + label Dibuat
- [x] Edit modal: form sama dengan Tambah
- [x] Fix: user_id auto-set di model (error default value)

## Fase 13: Input SJ Supplier — Integrasi + Polish ✅

- [x] Auto-create SupplierSj saat TaskTerimaSupplier status jadi SELESAI
- [x] Prefix SJSUP + id_task auto-generate
- [x] Kolom jumlah_koli, jumlah_faktur di SupplierSj
- [x] Status: belum_di_cek (gray), draft (warning), selesai (success)
- [x] Kolom Tempo — selisih hari (badge merah/hijau, format: blm input / input X hr)
- [x] Ref Terima Supplier — parse TRM-SUP-xxxxx dari keterangan
- [x] Lama Bongkar — selesai_bongkar - jam_bongkar (jam:menit)
- [x] tangal_input → maxDate(now()) — proteksi tanggal maju
- [x] PO wajib jika status Selesai — validasi action + form
- [x] Sync PO dua arah: SupplierSj ↔ TaskTerimaSupplier
- [x] ActivityLog description: varchar(255) → TEXT
- [x] no_po_referensi → nullable
- [x] nomor_sj di BranchShipment → nullable
- [x] requiredIf status selesai

## Fase 14: Checker Keluar Barang — Refactor ✅

- [x] Migration: tambah branch_shipment_id (FK) + jam_disiapkan + diserahkan_kepada + helper (JSON)
- [x] Migration: drop kolom lama (toko_tujuan, supplier, no_referensi_sj, jumlah_kolian, jam_naik, nama_koordinator)
- [x] Migration: ubah status ke enum('draft','siap kirim','selesai') default draft
- [x] Migration: tambah cabang/nomor_sj/total_qty/no_po (copy dari BranchShipment)
- [x] Form: Select BranchShipment (status=selesai, exclude already processed)
- [x] Form: auto-fill cabang, nomor_sj, total_qty, no_po (disabled, dehydrated)
- [x] Field baru: jam_disiapkan, status, diserahkan_kepada (textbox), helper (Select multiple)
- [x] Single form modal (no Repeater)
- [x] Edit mode: dropdown disabled + options include current record
- [x] ViewAction: Section "Informasi Task" 2 kolom (seragam)
- [x] Helper di View: badge + separator(', ') + tooltip nama lengkap
- [x] Helper di Grid: max 2 + +N more badge + tooltip all names

## Fase 15: Kiriman Mobil — Refactor ✅

- [x] Migration: tambah jam_tiba + status (draft/dalam pengiriman/datang)
- [x] Migration: pivot branch_shipment_kiriman_mobil (many-to-many)
- [x] Migration: no_plat_mobil, jam_muat, jam_selesai, jam_berangkat, nama_supir → nullable
- [x] FK keluar_barang_id → task_keluar_barangs (auto-create tracking)
- [x] Form: Pilih SJ (Select multiple) filter by cabang + exclude already assigned
- [x] Display-only: total SJ dipilih, sisa SJ kiriman, durasi kiriman
- [x] Field jam_tiba + durasi kiriman (auto-hitung dari berangkat→tiba)
- [x] Status options: draft/dalam pengiriman/selesai (datang dihapus)
- [x] Badge status: draft(gray), dalam pengiriman(warning), selesai(success)
- [x] EditAction: using() callback untuk sync pivot
- [x] Single form modal (no Repeater)
- [x] Sisa SJ: hitung dari tersedia + attached record (real sisa)
- [x] Options SJ: include record saat Edit (orWhere)
- [x] afterStateHydrated: set state dari pivot table
- [x] Cabang disabled di Edit mode
- [x] Kolom SJ di grid: badge + tooltip, max 2 + +N more
- [x] Retur_option: simpan ke DB (tidak auto-create retur)

## Fase 16: Polish & Fixes ✅

- [x] Checker Keluar Barang: flow pilih cabang dulu → SJ filtered by cabang
- [x] Checker Keluar Barang: disabled fields dehydrated(true) — data tersimpan
- [x] Checker Keluar Barang: exclude already processed SJs (whereNotIn task_keluar_barangs)
- [x] Checker Keluar Barang: options include record saat Edit
- [x] Checker Keluar Barang: cabang disabled saat Edit
- [x] Kiriman Mobil: status ENUM update (datang→selesai)
- [x] Kiriman Mobil: icons di form (11 fields)
- [x] Checker Keluar Barang: icons di form (10 fields)
- [x] BranchShipment: opsi RB/Pesanan di Pilih Kiriman
- [x] BranchShipment: No PO column visible di grid (pindah posisi)
- [x] Rename Kiriman Mobil status badge (datang→selesai)
- [x] Hapus auto-create Kiriman Mobil — Checker Kiriman input manual
- [x] Buat `.agents/alur-pengiriman.md` — dokumentasi alur ke user
- [x] Dropdown no_plat_mobil: "no plat - merek & model"
- [x] View modal no_plat_mobil: tampilkan "no plat - merek & model"
- [x] Auto-set status ke "Dalam Pengiriman" saat jam_berangkat diisi
- [x] Tambah field tanggal_kirim (nullable, no backdate)
- [x] Semua kolom Kiriman Mobil toggleable
- [x] Retur option disederhanakan: "Tidak Ada Retur" / "Ada Retur"
- [x] UI/UX Restructure: Kiriman Mobil → 4 Section (Data Kiriman, Waktu 3-col, Kendaraan, Status)
- [x] UI/UX Restructure: Input Kirim Barang → 2 Section (Data Kiriman, Status & Tanggal)
- [x] UI/UX Restructure: Checker Keluar Barang → 3 Section (Data SJ, Tim & Status)
- [x] UI/UX Restructure: Datang Mobil Supplier → 3 Section (Data Mobil, Waktu 3-col, Catatan)
- [x] UI/UX Restructure: Input SJ Supplier → 2 Section (Data Dokumen, Status Input)
- [x] TV Board: tambah link URL + copy + buka tab baru di halaman pengaturan
- [x] Hapus tombol "Input SJ Baru" — auto-create only dari Terima Supplier
- [x] Install Aurum Theme (Gold default → Sapphire)
- [x] Header actions: button hijau solid + size kecil via CSS
- [x] Fix Carbon month() type error di ManageLeaves + Blade view
- [x] Fix MasterKendaraan saved hook: tambah masa_berlaku di firstOrCreate
- [x] KIR: ubah masa berlaku dari 3 bulan jadi 6 bulan
- [x] KendaraanDokumen: tambah toggleable di kolom masa_berlaku
- [x] Helper badge warna orange (Checker Terima Supplier + Checker Keluar Barang)
- [x] Fix Retur Cabang edit: auto-fill kiriman_mobil_id, helpers proper pattern
- [x] Retur Cabang: cabang/no_plat_mobil jadi TextInput (biar nilai tampil di edit)
- [x] Retur Cabang: catatan_bagus, catatan_jelek, keterangan jadi nullable (form + DB)
- [x] Retur Cabang: dropdown kiriman_mobil_id tampilkan tanggal_kirim
- [x] Merge Retur Supplier: hapus Retur Keluar + Retur Masuk, buat menu Supplier Return In & Out
- [x] Cuti & Absensi: dropdown tahun dinamis dari database + searchable

## Fase 17: Export/Import ⏳

- [ ] Export semua master data (Ekspedisi, Kendaraan, Sopir, Toko)
- [ ] Import master data (Ekspedisi, Kendaraan, Sopir, Toko)
- [ ] Test semua role checker — akses sesuai role
- [ ] Ringkasan bulanan Cuti & Absensi (PDF/print)

## Fase 18: Export Table Plugin — DIBATALKAN ❌

Plugin `occtherapist/advanced-table-export-for-filament` pernah dicoba tapi **di-revert** karena UX modal tidak sesuai harapan. Digantikan custom export (Fase 23).

## Fase 23: Custom Export — XLSX & PDF ✅

Export custom tanpa plugin — 2 tombol **outlined kotak kecil** (label + icon) di toolbar, sejajar sebelum kolom search, langsung download sesuai filter aktif.

### Implementasi Service (`app/Services/TableExportService.php`)
- [x] `composer require dompdf/dompdf` (openspout sudah ada via `filament/actions`)
- [x] `streamXlsx()` — OpenSpout, chunk 500, StreamedResponse (menu query-based)
- [x] `streamPdf()` — HTML table → dompdf, A4 landscape, limit 200 baris
- [x] `resolveValue()` — data_get dot-notation, format Carbon (d/m/Y, H:i), array → comma
- [x] **`$formatters`** param — callback per kolom (helper ID → nama, status → label, computed)
- [x] **`streamXlsxFromRows()` / `streamPdfFromRows()`** — export dari array (untuk custom Page)
  - XLSX rows: **border semua sel + header bold** (OpenSpout Style/Border/BorderPart)
- [x] `resources/views/exports/table-pdf.blade.php` — PDF query-based
- [x] `resources/views/exports/table-pdf-rows.blade.php` — PDF array-based (landscape, font 7px)

### Deploy ke Menu (pola: 2 action di toolbarActions + exportColumns)
- [x] **TaskDatangMobilSuppliers** — Datang Mobil Supplier
- [x] **TaskTerimaSuppliers** — Checker Terima Barang Supplier (+ fix duplikat Supplier di modal)
- [x] **SupplierSj** — Input SJ Dari Supplier
- [x] **BranchShipment** — Input Kirim Barang
- [x] **TaskKeluarBarangs** — Checker Keluar Barang (formatter: helper → nama karyawan)
- [x] **TaskKirimanMobils** — Kiriman Mobil (formatters: total_sj, SJ list, status & retur → label)
- [x] **ManageLeaves** — Cuti & Absensi (Papan Absensi matrix)
  - Export di baris filter (icon outlined kecil + label, sejajar search)
  - `buildAbsensiMatrix()` — Karyawan × hari (C/S/I) + Sisa
  - XLSX ber-border + header bold

### Catatan
- Export menghormati filter aktif + scope role (via `getFilteredTableQuery()`)
- XLSX unlimited, PDF max 200 baris
- Belum (menunggu konfirmasi): border di XLSX menu lain (masih khusus Cuti & Absensi)

## Fase 18: Fine-Grained RBAC (Per-Menu & Per-Action) ✅

### Ringkasan
Permission-based access control per-module & per-action, dikelola melalui UI Edit User. Admin bisa menentukan menu mana yang bisa diakses user berikut capability (view/create/update/delete) tanpa tergantung role. **Terimplementasi penuh & terverifikasi.**

### Tasks

- [x] **Permission Seeder** — `database/seeders/PermissionSeeder.php`: 84 permission (`view|create|update|delete_{module_key}` = 80 + 4 widget), terdaftar di `DatabaseSeeder` setelah `RoleSeeder`
- [x] **UI Edit User — Section "Akses Menu & Fitur"** — checkbox tree (Group → Menu → Actions) dengan toggle per action:
  - Section group per nav-group (Master, Purchasing Order, Retur, Penerimaan, Pengiriman, Administrasi, Pengaturan, Dashboard & Widgets)
  - Per modul: 5 kolom = **Pilih Semua** + View + Create + Update + Delete
  - Widget: checkbox "Aktif" mandiri per widget
  - State disimpan sebagai permission langsung user di Spatie `model_has_permissions` via `syncPermissions()` (field berprefix `perm_*`)
- [ ] **ListAkses User** — halaman grid/list permission yang sudah diset per user *(belum dibuat — optional/future)*
- [x] **Update Semua Resource** — ganti hardcoded role check dengan `auth()->user()->can('{action}_{module}')` di 18 resource + 2 custom page (ManageLeaves, ManageTvBoard) + 4 widget:
  - `canViewAny()` → `can('view_*')`
  - `canCreate()` → `can('create_*')`
  - `canEdit()` → `can('update_*')`
  - `canDelete()` → `can('delete_*')`
  - `shouldRegisterNavigation()` → `can('view_*')`
  - `getEloquentQuery()` → tetap filter own data untuk non-Admin (kondisi `hasRole('Admin')` dipertahankan)
- [x] **Default Role Permissions** — `PermissionSeeder::assignRoleDefaults()`: Admin = semua 84, checker sesuai fungsinya (Terima=19, Retur/Keluar=11, Kiriman=7)
- [x] **Admin bypass** — `AppServiceProvider::boot()`: `Gate::before` return `true` untuk role Admin
- [x] **Test semua role + custom user** — smoke test: Admin full, Checker Terima hanya modulnya, widget Admin-only deny untuk checker; panel boot OK

## Fase 25: Role Dinamis + Akses Nempel di User ✅

### Ringkasan
Role menjadi **dinamis** (CRUD via UI) dan berhenti mewariskan akses. **Akses = 100% direct permission user** (role = label + `is_super_admin` + `permission_template` untuk pre-fill saat pilih role).

### Tasks

- [x] **Migrasi** — `roles` + `is_super_admin` (bool) + `permission_template` (json); `decouple_role_permissions_to_user`: salin permission role → template + direct user lama, kosongkan `role_has_permissions`
- [x] **Seeder** — +`view_all_data` (total 85), `setRoleDefaults()` → template per role + `is_super_admin` Admin
- [x] **`User::isSuperAdmin()`** + `Gate::before` → `isSuperAdmin()` (bukan `hasRole('Admin')`)
- [x] **`PermissionMenu`** (shared helper) — flat tree: Akses Global + Group header non-collapsible + menu (Pilih Semua + 4 action) + widget Aktif; dipakai UserForm & RoleForm
- [x] **RoleResource** — CRUD role dinamis (nama, super admin toggle, permission template); hanya super admin
- [x] **UserForm** — role Select `live()` pre-fill dari template; menu flat (bukan group collapsible); `view_all_data`; widget per-widget
- [x] **Decouple hardcode** — `getEloquentQuery()` → `can('view_all_data')`; `StatsOverviewWidget` generik per-modul (own vs all); `DeleteBulkAction`/kolom Checker → `isSuperAdmin()`
- [x] **Verifikasi** — 85 permission, role_has_permissions=0, admin super_admin + direct 84, terima direct 19, panel boot OK

## Fase 20: Filter AboveContent + UI Polish ✅

- [x] **BranchShipment (Input Kirim Barang)** — filter AboveContent: Kiriman, Cabang, Tanggal, Status; 5 kolom
- [x] **TaskKeluarBarangs (Checker Keluar Barang)** — filter AboveContent: Cabang, Tanggal, Status; 3 kolom
- [x] **TaskKirimanMobils (Kiriman Mobil)** — filter AboveContent: Cabang, Tgl Kirim, Retur, Status; 4 kolom + helper text
- [x] **TaskTerimaSuppliers** — pisah kolom Supplier/Ekspedisi; toggleable semua kolom; filter AboveContent: Supplier, Status, Tanggal; 4 kolom
- [x] **TaskDatangMobilSuppliers** — filter AboveContent: Supplier, Ekspedisi, Jenis Kiriman, Tgl Datang, Status; 5 kolom
- [x] **SupplierSj (Input SJ Supplier)** — filter AboveContent: Nama Supplier, Tgl Input, Status; 3 kolom; toggleable semua kolom; description auto-created
- [x] CSS light mode — pertebal border input/modal/table (1.5px #C8C4BC)
- [x] Footer — text center "© 2026 jobdesk MSK. All rights reserved."

## Fase 19: Purchasing Order — Komplain PO ✅

- [x] Grup navigasi baru **Purchasing Order** (sebelum Retur)
- [x] Menu **Komplain PO** dengan icon `document-text`
- [x] Halaman "Coming Soon" pending pengembangan fitur
- [x] Implementasi modul Komplain PO lengkap (Gudang ↔ Admin PO)
- [x] Migration `po_complaints` + model `KomplainPo` + TaskIdGenerator prefix `KMPL`
- [x] Resource `KomplainPoResource` (grup Purchasing Order)
- [x] Form 3 Section: PO Supplier, Barang, Status
- [x] Foto min 1 max 5 (disk public `fotos-komplain/`, ImageEntry view + tooltip grid)
- [x] Logika status: Selesai hanya jika `tanggal_datang_barang` terisi (select disabled)
- [x] Export XLSX + PDF
- [x] Akses RBAC: permission `komplain_pos` (default Admin + Checker Terima, via PermissionSeeder)

## Fase 24: Bug Fixes Menu Lain ✅

- [x] Komplain PO: fix tabel model (protected $table = 'po_complaints')
- [x] Komplain PO: fix icon barcode → qr-code
- [x] Komplain PO: fix error container (hapus action kolom foto → ImageEntry)
- [x] Sticky header matrix Cuti & Absensi (floating saat scroll)
- [x] Hapus warna weekend di matrix
- [x] Fix tempo SupplierSj stabil saat status selesai
- [x] Fix STNK 1 tahun = 5 tahun − 4 tahun (menu Masa Berlaku)

## Fase 21: Deployment Hosting + Fix 403 ✅

- [x] Deploy ke Hostinger (`gudang.mutiarasuperkitchen.com`)
- [x] Fix 403 di production — **User model harus implement `FilamentUser` + `canAccessPanel()`**
- [x] Fix session hosting — `SESSION_DRIVER=database`, `encryptCookies(except)` untuk session cookie
- [x] Fix TrustProxies — `bootstrap/app.php` trustProxies untuk proxy Hostinger
- [x] Redirect `/` ke `/admin`
- [x] Logo `logo_msk.png` di login & sidebar
- [x] Debug session/auth (route sementara, sudah dihapus)
- [x] Docs update: PRD, TASKS, GEMINI_KNOWLEDGE, README

## Fase 22: Dashboard — Aurum Widgets ✅

- [x] **StatsOverviewWidget** — upgrade ke `AurumStatsOverview` + `AurumStat`
  - Admin: 9 kartu (Retur ke/dari Supplier, Terima, Keluar, Kiriman, Retur Masuk Cabang, Datang Mobil, SJ Belum Di Cek, STNK/KIR ≤30 hari)
  - Checker: kartu sesuai role (data sendiri)
  - Full width (`columnSpan = 'full'`)
  - Grid responsif: mobile 1 kolom, tablet 2, desktop 5 per baris
- [x] **ExpiringDocumentsWidget** (AurumValueList) — STNK/KIR ≤7 hari atau EXPIRED
  - Admin only, half width, klik → menu Masa Berlaku STNK/KIR
- [x] **LeavesTodayWidget** (AurumValueList) — Cuti/Sakit/Izin hari ini
  - Admin only, half width, tampil divisi, klik → Cuti & Absensi
- [x] Layout dashboard: Stats (full) → [Expiring | Cuti] → RecentActivity (full)

## Fase 25: Fix KIR + Filter & Badge Retur Toko ✅

### Masa Berlaku STNK/KIR — aturan KIR
- [x] **Grid Masa Berlaku:** KIR hanya tampil jika `masa_berlaku` terisi (`modifyQueryUsing` → `where jenis != kir OR masa_berlaku != null`)
  - Motor → KIR tersembunyi (tidak ada perpanjang KIR)
  - Mobil pribadi (tidak isi masa_berlaku_kir) → KIR tersembunyi
  - Mobil isi masa_berlaku_kir → KIR otomatis muncul
- [x] **Form Master Kendaraan:** field `no_kir` & `masa_berlaku_kir` hanya utk **mobil** (`->visible(fn ($get) => $get('jenis_kendaraan') !== 'motor')`, `jenis_kendaraan` → `->live()`)
- [x] **Grid Master Kendaraan:** kolom `no_kir`/`masa_berlaku_kir` + modal hidden jika `masa_berlaku_kir` kosong (`$record?->` null-safe)
- [x] **Model MasterKendaraan:** `createDokumenRecords()` & `saved` hook — tidak buat/sync KIR utk motor
- [x] Fix error: `visible()` column null-safe (`$record?->masa_berlaku_kir`), type-hint `Get` dihapus (Filament v5 pakai `Schemas\Utilities\Get`)

### Retur Masuk dari Toko
- [x] **Filter AboveContent 1 baris:** Toko, Jenis Retur (+ opsi RB dan RJ), Status, Tanggal (Grid Dari/Sampai) — `filtersFormColumns(4)`
- [x] **Dropdown Kiriman Mobil badge pill** (gaya Checker Terima): toko pill hijau `#22c55e`, "tgl kirim : ..." pill merah `#ef4444`, `->allowHtml()`

## Fase 26: Users & Roles — Konversi ke Modal + Tabs ✅

- [x] Hapus halaman Create/Edit User & Role (`getPages()` hanya `index`) → Create/Edit jadi **modal**
- [x] `UserForm` & `RoleForm` pakai **Tabs** + `columnSpanFull()` (fix isi miring ke kiri karena resolver modal memaksa `columns(2)`)
  - User: *Informasi* (4 kolom Name|Email|Password|Role) / *Akses Menu & Fitur* / *Dashboard & Widgets*
  - Role: *Informasi* (Nama + Super Admin) / *Detail Template*
- [x] `PermissionMenu` → **matriks compact**: `globalSection()` / `menuSections()` (Group collapsible default ciut, modul `Fieldset` 5 kolom: Pilih Semua|Lihat|Tambah|Ubah|Hapus, dalam `Grid(2)`) / `widgetsSections()`
- [x] Sync permission/role dipindah ke `CreateAction::using()` + `EditAction::using()` (checkbox `perm_*` kini **dehydrated** ikut `$data`; `select_all_*` tetap UI-only)
- [x] Modal polish: `Width::Full`, `createAnother(false)`, heading/description eksplisit
- [x] Roles: log manual create/update/delete via `ActivityLogger`

## Fase 27: Activity Log — Otomatis Semua Menu + Widget Mudah Dibaca ✅

- [x] `App\Services\ActivityLogger` — `log/created/updated/deleted`; diff pakai label Bahasa Indonesia (`fieldLabel`); `moduleLabel` ikut label menu (`PermissionMenu`); `prettifyDescription` rapikan data lama; skip jika tidak ada user
- [x] `App\Models\Concerns\LogsActivity` — trait auto-log `created/updated/deleted`; config per model via method static (`activityModule`, `activityTracked`, `activitySummaryAttributes`, `activityReferenceField`, `shouldLogActivity`)
- [x] Pasang di semua model: 6 task (Retur Cabang, Retur Supplier, Datang, Terima, Keluar, Kiriman) + BranchShipment, SupplierSj, KomplainPo, WarehouseDocument, KendaraanDokumen (skip doc otomatis `user_perpanjang='System'`), WarehouseLeave, Master (Supplier, Toko, Kendaraan, Sopir, Ekspedisi, Employee), User
- [x] Hapus blok `ActivityLog::create` manual di model task lama — tidak ada log ganda
- [x] `RecentActivityWidget` — kolom **Aksi** (badge+ikon `Dibuat`/`Diubah`/`Dihapus`), **Menu** (badge+ikon label menu), **Aktivitas** (wrap + prettify), ID, Referensi, User, Waktu; filter Menu dinamis + User + Rentang Waktu; pagination [10,25,50]

## Fase 28: Status Online User (cache) ✅

- [x] `App\Http\Middleware\TrackLastActive` — tiap request authenticated `Cache::put('user-online-{id}', …, 10 menit)`; didaftarkan di `authMiddleware` panel
- [x] `User::isOnline()` — baca cache (tanpa migration baru)
- [x] Kolom **Status** di grid Users — badge hijau `Online` (+wifi) / abu `Offline` (+signal-slash)

## Fase 29: Fix Header Grup Masa Berlaku STNK/KIR ✅

- [x] Hapus mojibake emoji (`ðŸš—`) di group title periode (`KendaraanDokumensTable`) → label bersih `STNK 1 Tahun` / `STNK 5 Tahun (Acuan)` / `KIR`
- [x] Group header tabel dibuat **bold** via CSS `.fi-ta-group-header .fi-ta-group-heading { font-weight: 700 }`

## Fase 30: Fix Gate Atur Saldo Cuti ✅

- [x] Tab "Atur Saldo Cuti" (`ManageLeaves`) + method `adjustJatahCuti` + tombol Adjust di blade — di-gate permission `update_cuti_absensi`

## Fase 31: Komplain PO — Label Penyelesaian + Helper ✅

- [x] Label `Tanggal Datang Barang` → **`Tanggal Penyelesaian`** (form create/edit) + helperText kunci status Selesai
- [x] Grid kolom, modal View, header export → `Tgl Penyelesaian`
- [x] Helper **Foto Barang**: "Upload min 1 max 5 foto bukti komplain (JPG/PNG)"

## Fase 32: Retur Masuk dari Toko — Modal 75% + Guard Kiriman ✅

- [x] Modal create & edit → **`Width::SevenExtraLarge`** (seragam)
- [x] Data Retur: `Jenis Retur | Tanggal Bongkar | Jam Bongkar` 1 baris; **Helper full-width**
- [x] Jumlah SJ + Catatan dibungkus Fieldset **Retur Bagus/Retur Jelek** (berdampingan saat `rb_dan_rj`, `Jumlah SJ | Catatan` sejajar)
- [x] Simpan FK **`kiriman_mobil_id`** (migration + backfill cabang+plat unik) → dropdown kiriman `whereDoesntHave('taskReturCabangs')`; edit include record
- [x] `no_plat_mobil`/`jam_tiba`/`nama_sopir` nullable + fallback `nama_supir ?? ''` (fix error 1048); Create/Edit tidak lagi unset `kiriman_mobil_id`; hydration pakai FK (fallback cabang+plat utk lawas)

## Fase 33: Retur In & Out Supplier — UX + Sync Truk ✅

- [x] Modal `Width::SevenExtraLarge`
- [x] Data Supplier: baris 1 `Pilih Mobil | Jenis Pengiriman*`; autofill `Supplier|Ekspedisi|Supir | Plat|Tgl Datang|Jam Kedatangan` (2 baris × 3 kolom)
- [x] `Jenis Pengiriman*` pindah ke **Detail Retur** (full, live, helperText); baris `No Nota|Status`; **Fieldset "Retur Keluar"/"Retur Masuk"** (berdampingan saat `datang_dan_keluar`)
- [x] `Potong Nota` pindah ke **Retur Keluar** (badge danger); Retur Masuk = Servis/Ganti Barang
- [x] `SupplierReturn` create/update/delete → `truck->syncStatus()`

## Fase 34: Datang Mobil — Status Otomatis + Sync 🔄

- [x] Hapus field `status` form → truk baru `MENGANTRI` (otomatis)
- [x] Modal `Width::SevenExtraLarge`
- [x] `syncStatus()` ditulis ulang: truk SELESAI bila semua kewajiban beres per `jenis_kiriman` — `DATANG` (terima selesai), `RETUR` (retur selesai), `DATANG & RETUR` (**keduanya**); RETUR masih draft/belum → PROSES
- [x] Guard hapus truk ditambah untuk **SupplierReturn** (mirror Terima)
- [ ] Command `app:sync-truck-status` (healing status lama) — **pending**

## Fase 35: Input SJ — Proteksi Delete + Lifecycle ✅

- [x] DeleteBulk dihapus dari tabel SJ (data otomatis dari Terima)
- [x] Terima jadi **draft** / Terima **dihapus** → SJ terkait **ikut terhapus**
- [x] Edit SJ `Selesai` tanpa No PO → `throw ValidationException` (error di field, modal tidak tutup); No PO boleh menyusul manual

## Fase 36: Filter Supplier Retur + MaxDate Bongkar + Widget Datang Mobil 43/40 ✅

- [x] **Retur In & Out Supplier** — tambah filter **Supplier** (dropdown searchable dari `nama_supplier` distinct) di posisi **sebelum Jenis**; `filtersFormColumns(2→3)` → 1 baris `Supplier | Jenis | Tanggal Buat`
- [x] **Retur Masuk dari Toko** — `Tanggal Bongkar` tambah `maxDate(now())` (picker tolak tanggal masa depan) + rule server-side `before_or_equal:today` (create & edit, form sumber sama)
- [x] **Widget "Datang Mobil"** (StatsOverviewWidget) — value format `{total}/{status SELESAI}` → **`43/40`**; angka selesai (40) di-render **hijau** via override blade `resources/views/vendor/aurum-filament-theme/widgets/stats-overview.blade.php` (`{!! !!}` utk value) + CSS `.aurum-stat-value--green { color:#22c55e }`; hormati scope own/all per user
- [x] **Widget "Terima Barang"** — mirror Datang Mobil: value `{total}/{status SELESAI}` (mis. `14/{selesai}`, termasuk draft), angka selesai **hijau** (`aurum-stat-value--green`); desc "Total terima / task selesai"
- [x] **⚡ Aktivitas Terakhir** (RecentActivityWidget) — pagination **default 50**, opsi **50 / 100 / 200 / All** (`->paginated([50,100,200,'all'])`)

## Fase 37: Berita Acara Komplain PO 🆕 (PENDING — menunggu komunikasi team gudang & admin PO)

### Tujuan
Cetak data Komplain PO menjadi dokumen resmi **Berita Acara** untuk diserahkan ke supplier sebagai bukti komplain barang dari PO datang.

### Catatan
- Diblokir sementara: perlu konfirmasi kebutuhan dengan team gudang & admin PO (isi, format, pihak penandatangan, nomor BA).

### Rencana awal
- [ ] Tombol **"Cetak Berita Acara"** per baris Komplain PO (ikon printer)
- [ ] Template `resources/views/exports/berita-acara-komplain.blade.php` (A4 portrait): header perusahaan + judul BA + no/tanggal + pernyataan + tabel data komplain + foto bukti + blok tanda tangan Supplier & Petugas Gudang
- [ ] Method baru `TableExportService::streamPdfRecord()` (reuse dompdf)
- [ ] (opsional) tombol di modal Detail + cetak semua sesuai filter
- [ ] Foto via path absolut/base64 (dompdf)


