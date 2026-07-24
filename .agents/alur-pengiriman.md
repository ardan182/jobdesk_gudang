# 📦 Panduan Pengiriman Barang — Grup PENGIRIMAN

Alur Pengiriman terdiri dari **3 langkah berurutan**:

```
Input Kirim Barang → Checker Keluar Barang → Kiriman Mobil
```

---

## Langkah 1: Input Kirim Barang

**Akses:** Menu **Pengiriman → Input Kirim Barang**

**Fungsi:** Mencatat rencana pengiriman barang ke cabang.

### Form Input:

| Field | Tipe | Wajib? | Keterangan |
|-------|------|--------|-----------|
| **Pilih Kiriman** | Select | Ya | Sumber barang: `Pembagian dari PO`, `Stock Gudang`, atau `RB / Pesanan` |
| **Cabang** | Select (searchable) | Ya | Pilih toko/cabang tujuan. **Tidak bisa diubah setelah disimpan** |
| **Nomor SJ** | Text | Required if status=Selesai | Nomor Surat Jalan |
| **Total Qty** | Numeric | Ya | Jumlah barang/koli |
| **No PO** | Text | Tidak | Nomor PO Pembelian (opsional) |
| **Tanggal Buat** | Date | Ya | Default: hari ini |
| **Status** | Select | Ya | `Draft` (default) / `Selesai` |
| **Keterangan** | Textarea | Tidak | Catatan tambahan |

### ID Task
Format: `KRM-BRG-XXXXX` (contoh: `KRM-BRG-00001`)

### Status Badge
| Status | Warna |
|--------|-------|
| Draft | 🟡 Kuning |
| Selesai | 🟢 Hijau |

### Badge Pilih Kiriman
| Opsi | Warna |
|------|-------|
| Pembagian PO | 🔵 Biru |
| Stock Gudang | 🟡 Kuning |
| RB / Pesanan | 🔴 Merah |

> 💡 **Tips:** Set status jadi **Selesai** agar bisa diproses oleh Checker Keluar Barang.

---

## Langkah 2: Checker Keluar Barang

**Akses:** Menu **Pengiriman → Checker Keluar Barang**

**Fungsi:** Mencatat proses persiapan dan penyerahan barang yang akan dikirim.

### Form Input:

| Field | Tipe | Wajib? | Keterangan |
|-------|------|--------|-----------|
| **Cabang** | Select (searchable) | Ya | Pilih cabang tujuan. **Tidak bisa diubah setelah disimpan** |
| **Pilih SJ** | Select (searchable) | Ya | Pilih Surat Jalan yang **sudah Selesai** dari Input Kirim Barang → **No SJ, Qty, No PO terisi otomatis** |
| **No SJ** | Text (disabled) | — | Terisi otomatis dari SJ yang dipilih |
| **Total Qty** | Numeric (disabled) | — | Terisi otomatis dari SJ yang dipilih |
| **No PO** | Text (disabled) | — | Terisi otomatis dari SJ yang dipilih |
| **Jam Disiapkan** | TimePicker | Ya | Waktu barang mulai disiapkan |
| **Status** | Select | Ya | `Draft` (default) / `Siap Kirim` / `Selesai` |
| **Diserahkan Kepada** | Text | Tidak | Nama koordinator penerima |
| **Helper** | Select (multi) | Tidak | Pilih karyawan pembantu (bisa lebih dari 1) |
| **Keterangan** | Textarea | Tidak | Catatan kondisi proses |

### ID Task
Format: `KLR-XXXXX` (contoh: `KLR-00001`)

### Status Badge
| Status | Warna |
|--------|-------|
| Draft | ⚪ Abu-abu |
| Siap Kirim | 🟡 Kuning |
| Selesai | 🟢 Hijau |

---

## Langkah 3: Kiriman Mobil

**Akses:** Menu **Pengiriman → Kiriman Mobil**

**Fungsi:** Mencatat detail pengiriman menggunakan mobil — dari muat hingga tiba, termasuk opsi retur.

### Form Input:

| Field | Tipe | Wajib? | Keterangan |
|-------|------|--------|-----------|
| **Cabang** | Select (searchable) | Ya | Terisi otomatis dari Checker Keluar Barang. **Tidak bisa diubah setelah disimpan** |
| **Pilih SJ** | Select (multi) | Ya | Pilih satu/lebih SJ — counter otomatis menampilkan **Total SJ** dan **Sisa SJ** |
| **Total SJ Tampil** | Text (display) | — | Jumlah SJ yang sudah dipilih |
| **Sisa SJ Tampil** | Text (display) | — | Sisa SJ yang tersedia untuk cabang ini |
| **Jam Muat** | TimePicker | Tidak | Waktu mulai memuat |
| **Jam Selesai Muat** | TimePicker | Tidak | Waktu selesai memuat |
| **No Plat Mobil** | Select | Tidak | Pilih kendaraan |
| **Nama Supir** | Select | Tidak | Pilih sopir |
| **Jam Berangkat** | TimePicker | Tidak | Waktu keberangkatan |
| **Jam Tiba** | TimePicker | Tidak | Waktu tiba di tujuan → **Durasi otomatis terhitung** |
| **Durasi Tampil** | Text (display) | — | Durasi perjalanan (contoh: `2j 30m`) — otomatis |
| **Status** | Select | Ya | `Draft` (default) / `Dalam Pengiriman` / `Selesai` |
| **Retur Option** | Select | Hanya jika status=Selesai | `Tidak Ada Retur` / `Ada RB` / `Ada RJ` / `RB dan RJ` |
| **Keterangan** | Textarea | Tidak | Catatan |

### ID Task
Format: `KRM-XXXXX` (contoh: `KRM-00001`)

### Status Badge
| Status | Warna |
|--------|-------|
| Draft | ⚪ Abu-abu |
| Dalam Pengiriman | 🟡 Kuning |
| Selesai | 🟢 Hijau |

### Retur Option Badge
| Opsi | Warna |
|------|-------|
| Tidak Ada Retur | ⚪ Abu-abu |
| Ada RB | 🟡 Kuning |
| Ada RJ | 🔵 Biru |
| RB dan RJ | 🔴 Merah |

---

## Diagram Alur Lengkap

```
┌─────────────────────────────────────────────────────────┐
│                   PENGIRIMAN BARANG                       │
├─────────────────────────────────────────────────────────┤
│                                                           │
│  [1] INPUT KIRIM BARANG                                   │
│      ┌─────────────────────────────────────┐              │
│      │  Pilih Kiriman → Cabang → No SJ     │              │
│      │  Qty → PO (opsional) → Status      │              │
│      └──────────────┬──────────────────────┘              │
│                     ▼ status = Selesai                     │
│                                                           │
│  [2] CHECKER KELUAR BARANG                                │
│      ┌─────────────────────────────────────┐              │
│      │  Pilih Cabang                       │              │
│      │  Pilih SJ (auto-fill No SJ, Qty, PO)│              │
│      │  Jam Disiapkan → Helper → Status    │              │
│      └──────────────┬──────────────────────┘              │
│                     ▼ status = Selesai                     │
│                     (proses Checker selesai)                │
│                                                           │
│  [3] KIRIMAN MOBIL                                         │
│      ┌─────────────────────────────────────┐              │
│      │  Data cabang + SJ terisi otomatis   │              │
│      │  Tambah SJ lain (multi-select)      │              │
│      │  Plat Mobil → Supir                │              │
│      │  Jam Muat → Selesai Muat           │              │
│      │  Jam Berangkat → Jam Tiba         │              │
│      │    (durasi otomatis)               │              │
│      │  Status → Retur Option (jika perlu)│              │
│      └─────────────────────────────────────┘              │
│                                                           │
└─────────────────────────────────────────────────────────┘
```

---

## Ringkasan Tabel Modul

| Langkah | Menu | ID Prefix | Role Pelaksana |
|---------|------|-----------|----------------|
| 1 | Input Kirim Barang | `KRM-BRG` | Admin / Checker Keluar |
| 2 | Checker Keluar Barang | `KLR` | Admin / Checker Keluar |
| 3 | Kiriman Mobil | `KRM` | Admin / Checker Kiriman |

---

## Master Data Pendukung

| Data | Sumber (Menu) | Digunakan di Modul |
|------|---------------|-------------------|
| **Cabang / Toko** | Master → Master Toko | Input Kirim Barang, Checker Keluar, Kiriman Mobil |
| **Kendaraan** | Master → Master Kendaraan | Kiriman Mobil (No Plat) |
| **Sopir** | Master → Master Sopir | Kiriman Mobil (Nama Supir) |
| **Helper** | Master → Master Employee Gudang | Checker Keluar Barang |

---

## Aturan Penting

1. **Urutan wajib:** Input Kirim Barang (Selesai) → Checker Keluar Barang (Selesai) → Kiriman Mobil
2. **Cabang tidak bisa diubah** setelah data tersimpan (Input Kirim Barang, Checker Keluar, Kiriman Mobil)
3. **Retur Option** hanya muncul jika status Kiriman Mobil = Selesai
4. **Data yang sudah dipakai** di Checker Keluar Barang tidak bisa dipilih lagi di Input Kirim Barang
5. **SJ yang sudah terpakai** di Kiriman Mobil lain tidak bisa dipilih lagi