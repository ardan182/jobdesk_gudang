# TASKS — Jobdesk Gudang AP

> Status: ✅ Selesai | 🔄 In Progress | ⏳ Planned

---

## Fase 1: Foundation ✅

- [x] Init Laravel 13 + Filament v5 + Spatie Permission
- [x] Setup panel `/admin` — `AdminPanelProvider`
- [x] 5 role: Admin, Checker Retur, Checker Terima, Checker Keluar, Checker Kiriman
- [x] TaskIdGenerator — global sequential counter (5 digit)
- [x] 5 task migration + model + resource (Retur Supplier, Retur Cabang, Terima Supplier, Keluar Barang, Kiriman Mobil)
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

- [x] Semua task module: modal input (no Edit page)
- [x] ViewAction + EditAction via recordAction('view'), iconButton + tooltip
- [x] Datang Mobil Supplier (ARR-SUP) — baru, di grup Penerimaan
- [x] ID_TASK refactor — global counter, no_baris dropped

## Fase 4: UI/UX ✅

- [x] Column width optimization — `grow(false)` + `width()` di 11 tabel
- [x] Color-coded actions — `color('primary|warning|info|danger')`
- [x] Compact table + striped — `py-0.125rem` CSS + `striped()`
- [x] Sidebar navigation — collapsed by default via Alpine + localStorage
- [x] Sidebar persist — `collapsedGroups` disimpan
- [x] Nav group "Administrasi" ditambahkan

## Fase 5: Features ✅

- [x] Activity Log — RecentActivityWidget description `wrap` (no limit)
- [x] Soft deletes — implementasi lalu rollback (revert migration, discard files)
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
- [x] Sticky columns: nama karyawan (kiri), sisa cuti (kanan)
- [x] Striped rows
- [x] CSS full width: `.fi-main{padding-inline:0!important}`
- [x] Modal input — pilih karyawan, tanggal, tipe, keterangan
- [x] Validasi: minDate (no backdate), no duplicate, max 12 cuti/tahun
- [x] Delete dengan confirm + Alpine onClick

## Fase 7: Polish & Fixes

- [ ] Export semua master data (Ekspedisi, Kendaraan, Sopir, Toko) — belum ada
- [ ] Import master data (Ekspedisi, Kendaraan, Sopir, Toko) — belum ada
- [ ] Fitur edit untuk Datang Mobil Supplier — cek modal
- [ ] Test semua role checker — akses sesuai role
- [ ] Ringkasan bulanan Cuti & Absensi (PDF/print) — enhancement
