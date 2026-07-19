# Tech Spec — Jobdesk Gudang AP

**Versi:** 1.0 | **Tanggal:** 19 Juli 2026

---

## 1. Tech Stack

| Layer | Teknologi | Versi | Catatan |
|-------|-----------|-------|---------|
| Backend | Laravel | 13 | PHP 8.5.8 |
| Admin Panel | Filament | v5 | Auto-discover resources/pages/widgets |
| Database | MySQL / MariaDB | 8.0+ | Wajib `mysql`, bukan sqlite |
| Auth | Spatie Laravel Permission | - | 5 role: Admin, Checker Retur/Terima/Keluar/Kiriman |
| Frontend | Tailwind CSS + Alpine.js | - | Bundled via Filament |
| Assets | Vite | - | `npm run dev` / `npm run build` |
| Export/Import | ZipArchive + SimpleXML + DOMDocument | native PHP | Tidak pakai maatwebsite/phpspreadsheet |
| Code Graph | Graphify | 0.9.17 | 5800+ nodes, 18000+ edges |

---

## 2. Arsitektur

### Panel
- Single panel: `App\Providers\Filament\AdminPanelProvider`
- ID: `admin`, Path: `/admin`
- Primary color: `#EA580C` (orange)
- Sidebar collapsible, width `14rem`, groups collapsed by default via `Alpine.store('sidebar').collapsedGroups`
- Font: Arial (local), base size: 14px

### Navigation Groups (6)
Master (Admin) → Retur → Penerimaan → Pengiriman → Administrasi (Admin) → Pengaturan (Admin)

### Resource Structure
```
app/Filament/Resources/{Module}/
├── {Module}Resource.php        # Main resource
├── Pages/
│   └── List{Mодуle}.php       # List page (header actions)
├── Schemas/
│   └── {Module}Form.php       # Form schema
└── Tables/
    └── {Module}sTable.php     # Table configuration
```

### ID_TASK Generation
- Service: `app/Services/TaskIdGenerator.php`
- Format: `{PREFIX}-{NNNNN}` (5 digit global sequential counter)
- Prefixes: `RET-SUP`, `RET-CAB`, `ARR-SUP`, `TRM-SUP`, `KLR`, `KRM`
- 1 row = 1 unique ID (no batch share)

---

## 3. Database Schema

### Task Tables (6)
| Table | Prefix | Key Columns |
|-------|--------|-------------|
| `task_retur_cabangs` | RET-CAB | id_task, cabang, jenis_retur, no_sj_retur, jam_bongkar, nama_sopir |
| `task_retur_suppliers` | RET-SUP | id_task, nama_supplier_ekspedisi, no_plat_mobil, jam_muat, status(servis/tukar/pot_nota) |
| `arrival_supplier_trucks` | ARR-SUP | id_task, supplier_id(FK), expedition_id(FK), nama_sopir, jenis_kiriman, status(PROSES/SELESAI) |
| `task_terima_suppliers` | TRM-SUP | id_task, arrival_supplier_truck_id(FK), jam_datang, selesai_bongkar, status(selesai_tanpa_retur/selesai_ada_retur), lembar_sj |
| `task_keluar_barangs` | KLR | id_task, toko_tujuan, supplier, no_referensi_sj, jam_naik, status(komplit/kurang/lebih) |
| `task_kiriman_mobils` | KRM | id_task, cabang, no_plat_mobil, jam_muat, jam_selesai_muat, jam_berangkat |

### Master Tables (7)
| Table | Key Columns |
|-------|-------------|
| `suppliers` | kode_supplier(unique), nama_supplier, alamat, no_telepon |
| `expeditions` | nama_ekspedisi, no_telepon, alamat |
| `master_sopirs` | nama_sopir, no_whatsapp |
| `master_tokos` | nama_toko, alamat |
| `master_kendaraans` | nomor_polisi(unique), jenis_kendaraan, merek_dan_model, no_stnk, no_kir |
| `warehouse_employees` | nama_karyawan, division_id(FK), jatah_cuti(default 12) |
| `divisions` | nama_divisi(unique), keterangan |

### Support Tables
| Table | Purpose |
|-------|---------|
| `warehouse_leaves` | Cuti & Absensi (jenis_absen: Cuti/Sakit/Izin) |
| `activity_logs` | Activity audit trail |
| `supplier_sjs` | Input SJ dari Supplier |
| `supplier_return_inbounds` | Retur Masuk Supplier |
| `branch_return_outbounds` | Retur Keluar Cabang |
| `task_terima_supplier_helpers` | Pivot: TaskTerimaSupplier ↔ WarehouseEmployee |

---

## 4. Model Relations

```
ArrivalSupplierTruck
├── belongsTo: Supplier, Expedition, User

TaskTerimaSupplier
├── belongsTo: User, ArrivalSupplierTruck
├── belongsToMany: WarehouseEmployee (helpers → task_terima_supplier_helpers)

TaskKeluarBarang → belongsTo: User
TaskKirimanMobil → belongsTo: User
TaskReturCabang → belongsTo: User
TaskReturSupplier → belongsTo: User
ArrivalSupplierTruck → belongsTo: Supplier, Expedition, User

WarehouseEmployee
├── belongsTo: Division
├── hasMany: WarehouseLeave
├── belongsToMany: TaskTerimaSupplier (via helpers pivot)

WarehouseLeave → belongsTo: WarehouseEmployee, User
ActivityLog → belongsTo: User
```

---

## 5. Role Access Pattern

```php
canViewAny()           → hasRole('Admin') || hasRole('Checker X')
canDelete()            → only Admin
getEloquentQuery()     → where('user_id', auth()->id()) for non-Admin
shouldRegisterNavigation() → hasRole('Admin') || hasRole('Checker X')
```

---

## 6. Data Integrity Hooks

### TaskTerimaSupplier `deleted`
Revert ArrivalSupplierTruck ke PROSES saat record terima dihapus.

### ArrivalSupplierTruck `deleting`
Cegah hapus jika ada TaskTerimaSupplier yang mereferensi. Throw `ValidationException`.

---

## 7. UI Component Standards

### ViewAction (Detail Modal)
```php
ViewAction::make()
    ->iconButton()->tooltip('Lihat Detail')->color('info')
    ->modalHeading('Detail ...')
    ->modalSubmitAction(false)
    ->modalCancelAction(fn (Action $a) => $a->label('Tutup'))
    ->schema([
        Section::make('Judul')->columns(2)->schema([
            TextEntry::make('...')->label('...'),
        ]),
    ]),
```

### Create/Edit Form
```php
Section::make('Judul')
    ->description('...')
    ->icon('heroicon-o-...')
    ->columns(3)
    ->schema([
        Select::make('..._id')
            ->options(...)  // bukan relationship()
            ->searchable()->preload()
            ->reactive()
            ->afterStateUpdated(...),
        TextInput::make('...')
            ->prefixIcon('heroicon-m-...')
            ->disabled()->dehydrated(true),
    ]),
```

### Helpers Column (Table Grid)
```php
TextColumn::make('helpers_names')
    ->label('Helpers')
    ->badge()->color('success')
    ->getStateUsing(fn ($record) => { ... limit 2 + more }),
```
