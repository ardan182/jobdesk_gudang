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
- [x] TaskTerimaSupplier deleted → status truck jadi null (bukan PROSES)
- [x] Cegah double select mobil datang (whereNotIn excluded IDs)
- [x] Pusat Dokumen Edit modal konsisten (Width::Full + form eksplisit)

## Fase 11: Polish & Fixes ⏳

- [ ] Export semua master data (Ekspedisi, Kendaraan, Sopir, Toko)
- [ ] Import master data (Ekspedisi, Kendaraan, Sopir, Toko)
- [ ] Test semua role checker — akses sesuai role
- [ ] Ringkasan bulanan Cuti & Absensi (PDF/print)
