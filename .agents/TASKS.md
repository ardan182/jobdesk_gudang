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

## Fase 17: Export/Import ⏳

- [ ] Export semua master data (Ekspedisi, Kendaraan, Sopir, Toko)
- [ ] Import master data (Ekspedisi, Kendaraan, Sopir, Toko)
- [ ] Test semua role checker — akses sesuai role
- [ ] Ringkasan bulanan Cuti & Absensi (PDF/print)
