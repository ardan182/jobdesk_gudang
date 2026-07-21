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

---

## 8. Pusat Dokumen Module

### Migration
```php
$table->string('nama_dokumen');
$table->string('kategori');        // Formulir Lapangan, SOP Gudang, Template Import
$table->string('versi')->default('v1.0');
$table->string('file_path');
$table->string('format_file');     // auto dari pathinfo
$table->text('deskripsi')->nullable();
$table->integer('download_count')->default(0);
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
```

### Form FileUpload
```php
FileUpload::make('file_path')
    ->disk('local')
    ->directory('document_templates')
    ->storeFiles()
    ->acceptedFileTypes([...])
    ->maxSize(10240)
    ->required()
    ->columnSpanFull(),
```

### Create Action
```php
$data['format_file'] = strtolower(pathinfo($data['file_path'], PATHINFO_EXTENSION));
$data['user_id'] = auth()->id();
$this->getModel()::create($data);
```

### Download Action (Table)
```php
Action::make('download')
    ->icon('heroicon-m-arrow-down-tray')
    ->color('primary')
    ->iconButton()
    ->action(function ($record) {
        $record->increment('download_count');
        return Storage::disk('local')->download($record->file_path);
    }),
```

### Role Access
| Action | Admin | Checker |
|--------|-------|---------|
| View grid + download | ✅ | ✅ |
| Create / Edit / Delete | ✅ | ❌ |

---

## 9. Referensi

- [Filament v5 Documentation](https://filamentphp.com/docs/5.x/)
- [Filament v5 Actions / Edit](https://filamentphp.com/docs/5.x/actions/edit)
- [Filament v5 Tables](https://filamentphp.com/docs/5.x/tables)
- [Filament v5 Forms](https://filamentphp.com/docs/5.x/forms)
- [Filament v5 Infolists](https://filamentphp.com/docs/5.x/infolists)
- [Laravel 13 Documentation](https://laravel.com/docs/13.x)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v6/)
- [Filament Hub (Plugin Repository)](https://filament-hub.com/features/4.x)

---

## 10. BranchShipment (Input Kirim Barang)

### Migration
```php
Schema::create('branch_shipments', function (Blueprint $table) {
    $table->id();
    $table->string('id_task', 30)->nullable()->index();
    $table->enum('pilih_kiriman', ['pembagian_po', 'stock_gudang']);
    $table->string('cabang');
    $table->string('nomor_sj', 100);
    $table->integer('total_qty');
    $table->string('no_po', 100)->nullable();
    $table->date('tanggal_buat');
    $table->enum('status', ['draft', 'selesai'])->default('draft');
    $table->text('keterangan')->nullable();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
});
```

### TaskIdGenerator Prefix
`'branch_shipment' => 'KRM-BRG'` — format `KRM-BRG-00001`

### Model Boot Event
```php
static::creating(function ($model) {
    if (empty($model->id_task)) {
        $model->id_task = TaskIdGenerator::generate('branch_shipment');
    }
    if (empty($model->user_id)) {
        $model->user_id = auth()->id();
    }
});
```

### Form Fields
- `pilih_kiriman` — Select: Pembagian dari PO / Stock Gudang
- `cabang` — Select dari `MasterToko`
- `nomor_sj` — TextInput, required
- `total_qty` — TextInput numeric, required
- `no_po` — TextInput, nullable
- `tanggal_buat` — DatePicker, default now
- `status` — Select: Draft / Selesai, default draft
- `keterangan` — Textarea

### UI Modal Standards
- Create modal: `Width::Full` + Section 2 kolom
- Edit modal: `Width::Full` + `->form(getFormFields())` — identik dengan Create
- ViewAction: Section 2 kolom + tombol Tutup

---

## 11. Computed / Virtual Columns

### Tempo (SupplierSj — Input SJ Supplier)
```php
TextColumn::make('tempo')->badge()->color(...)
    ->getStateUsing(fn ($record) => {
        $days = abs(now()->startOfDay()->diffInDays($record->tanggal_datang));
        $prefix = in_array($record->status_input, ['belum_di_cek', 'draft']) ? 'blm input' : 'input';
        return "{$prefix} {$days} hr";
    }),
```
- **Rumus:** `abs(hari_ini - tanggal_datang)`
- **Badge:** merah (belum_di_cek/draft), hijau (selesai)
- **Tidak perlu kolom DB** — dihitung otomatis

### Lama Bongkar (TaskTerimaSupplier — Checker Terima)
```php
TextColumn::make('lama_bongkar')
    ->getStateUsing(fn ($record) => {
        $minutes = Carbon::parse($record->jam_bongkar)->diffInMinutes(Carbon::parse($record->selesai_bongkar));
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return $h > 0 ? "{$h}j {$m}m" : "{$m}m";
    }),
```
- **Rumus:** `selesai_bongkar - jam_bongkar` (dalam menit → dikonversi ke jam:menit)
- **3 lokasi:** Grid, ViewAction, Edit Form (disabled)
- Jika `selesai_bongkar` null → tampil `-`

---

## 12. SupplierSj Auto-Creation (Integrasi)

### Trigger
Di `TaskTerimaSupplier` model — `created` + `updated` event:
```php
if ($model->status === 'SELESAI') {
    \App\Models\SupplierSj::create([
        'nama_supplier'      => $arrivalTruck?->supplier?->nama_supplier,
        'tanggal_datang'     => $arrivalTruck?->tanggal_datang,
        'nomor_po_referensi' => $model->no_po_referensi,
        'jumlah_koli'        => $model->jumlah_kolian,
        'jumlah_faktur'      => $model->lembar_sj ?? 1,
        'status_input'       => 'belum_di_cek',
        'keterangan'         => 'Auto dari Terima Supplier: ' . $model->id_task,
    ]);
}
```

### Flow
| Skenario | SupplierSj terbuat? |
|----------|---------------------|
| Create langsung SELESAI | ✅ `created` event |
| DRAFT → Edit jadi SELESAI | ✅ `updated` event |
| DRAFT tetap | ❌ Tidak |

### Status Input Options (baru)
| Value | Label | Badge |
|-------|-------|-------|
| `belum_di_cek` | Belum Di Cek | `gray` |
| `draft` | Draft | `warning` |
| `selesai` | Selesai | `success` |
