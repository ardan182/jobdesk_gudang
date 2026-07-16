# 📋 Alur Program Jobdesk Gudang AP

**Tanggal:** 17 Juli 2026

---

## 🚪 Cara Masuk

1. Buka **Chrome** atau browser di komputer
2. Ketik alamat: **http://localhost:8000/admin**
3. Masukkan **email** dan **password** yang sudah dikasih
4. Klik tombol **"Sign in"**

---

## 🏠 Setelah Masuk

Akan muncul halaman **Dashboard** — isinya kartu-kartu angka:

- ✅ Berapa banyak tugas yang sudah dicatat hari ini
- 👑 Kalau **Admin**, lihat semua modul
- 👤 Kalau **Checker**, lihat total tugas yang dicatat sendiri

Di sebelah kiri ada **menu samping (sidebar)**. Ini daftar modulnya:

| Menu | Untuk Siapa |
|------|-------------|
| 📊 **Master** (6 menu) | Admin saja |
| 🔄 **Retur** (2 menu) | Checker Retur |
| 📥 **Penerimaan** (Datang Mobil + Terima) | Checker Terima |
| 📤 **Pengiriman** (Keluar + Kiriman) | Checker Keluar & Checker Kiriman |
| 📋 **Administrasi** (Cuti & Absensi) | Admin saja |
| ⚙️ **Pengaturan** (Users) | Admin saja |

---

## 🔄 Modul 1: Retur ke Supplier

**Dipake oleh:** Checker Retur
**Gunanya:** Mencatat barang retur yang dikirim balik ke supplier

### Cara Input

1. Klik menu **"Retur ke Supplier"** di sidebar kiri
2. Klik tombol **"Tambah Baris"** (warna oranye)
3. Isi data baris pertama:

| Kolom | Cara Isi |
|-------|----------|
| **Nama Supplier / Ekspedisi** | Ketik nama supplier atau ekspedisinya |
| **No Plat Mobil** | Ketik nomor plat mobil (contoh: B 1234 CD) |
| **Nama Sopir** | Ketik nama supirnya |
| **Jam Muat** | Pilih jam mulai memuat (contoh: 08:30) |
| **Jumlah Kolian** | Ketik jumlah barang/koli (contoh: 15) |
| **Admin SJ Retur** | Ketik nama admin yang buat SJ retur |
| **Status** | Pilih salah satu: 🔸 **Servis** (barang rusak) — 🔸 **Tukar** (barang ditukar) — 🔸 **Pot Nota** (dipotong dari tagihan) |
| **Keterangan** | Opsional — isi kalau ada catatan tambahan |

4. Klik **"Tambah Baris"** lagi kalau mau nambah baris berikutnya
5. Ulangi sampai semua baris terisi
6. Klik **"Simpan"** atau **"Create"**

### Cara Lihat Laporan

1. Klik **"Retur ke Supplier"** di sidebar
2. Otomatis muncul data **hari ini**
3. Mau lihat tanggal lain? Klik filter **Dari Tanggal** dan **Sampai Tanggal**, pilih tanggalnya
4. Bisa cari data: ketik di kolom **Search** (cari ID Task, nama supplier, plat, dll)
5. Bisa urutin: klik header kolom (misal klik **"Jam Muat"** biar urut jam)

### Cara Edit / Hapus

✏️ **Edit:** Klik icon pensil di baris yang mau diubah
🗑️ **Hapus:** Centang barisnya, klik tombol **"Hapus"**

---

## 🔄 Modul 2: Retur dari Cabang

**Dipake oleh:** Checker Retur
**Gunanya:** Mencatat barang retur yang datang dari cabang

### Cara Input

1. Klik menu **"Retur dari Cabang"** di sidebar
2. Klik tombol **"Tambah Baris"**
3. Isi data baris pertama:

| Kolom | Cara Isi |
|-------|----------|
| **Cabang** | Ketik nama cabang (contoh: Cimahi) |
| **Jenis Retur** | Pilih: 🔴 **Retur Jelek** (barang rusak) — 🟢 **Retur Bagus** (barang layak) |
| **No SJ Retur Indri/ERP** | Ketik nomor surat jalan retur |
| **Total Kolian** | Ketik jumlah barang/koli |
| **Jam Bongkar** | Pilih jam diturunkan (contoh: 10:15) |
| **Nama Sopir** | Ketik nama supirnya |
| **Keterangan** | Opsional |

4. Klik **"Tambah Baris"** kalau mau nambah lagi
5. Klik **"Simpan"**

---

## 📥 Modul 3: Terima Barang Supplier

**Dipake oleh:** Checker Terima
**Gunanya:** Mencatat barang yang diterima dari supplier

### Cara Input

1. Klik menu **"Terima Barang Supplier"** di sidebar
2. Klik tombol **"Tambah Baris"**
3. Isi data baris pertama:

| Kolom | Cara Isi |
|-------|----------|
| **Nama Supplier / Ekspedisi** | Ketik nama supplier |
| **No PO Referensi** | Ketik nomor PO pembelian |
| **Jumlah Kolian** | Ketik jumlah barang/koli diterima |
| **Jam Bongkar** | Pilih jam diturunkan |
| **Nama Sopir** | Ketik nama supirnya |
| **Status** | Pilih: 🟢 **Komplit** (sesuai PO) — 🔴 **Kurang** — 🟡 **Lebih** |
| **Keterangan** | Opsional |

4. Klik **"Simpan"**

---

## 📤 Modul 4: Keluar Barang

**Dipake oleh:** Checker Keluar
**Gunanya:** Mencatat barang yang dikeluarkan dari gudang ke toko / cabang

### Cara Input

1. Klik menu **"Keluar Barang"** di sidebar
2. Klik tombol **"Tambah Baris"**
3. Isi data baris pertama:

| Kolom | Cara Isi |
|-------|----------|
| **Toko Tujuan** | Pilih tujuannya: ⚪ **Pusat** — ⚪ **Ujungberung** — ⚪ **Soreang** — ⚪ **Majalaya** — ⚪ **Cicaheum** — ⚪ **Barokah** |
| **Supplier** | Ketik nama supplier barangnya |
| **No Referensi SJ** | Ketik nomor surat jalan |
| **Jumlah Kolian** | Ketik jumlah barang/koli |
| **Jam Naik** | Pilih jam barang naik ke mobil |
| **Nama Koordinator** | Ketik nama koordinatornya |
| **Status** | Pilih: 🟢 **Komplit** — 🔴 **Kurang** — 🟡 **Lebih** |
| **Keterangan** | Opsional |

4. Klik **"Simpan"**

---

## 🚛 Modul 5: Kiriman Mobil

**Dipake oleh:** Checker Kiriman
**Gunanya:** Mencatat pengiriman barang ke cabang per mobil

### Cara Input

1. Klik menu **"Kiriman Mobil"** di sidebar
2. Klik tombol **"Tambah Baris"**
3. Isi data baris pertama:

| Kolom | Cara Isi |
|-------|----------|
| **Cabang** | Ketik nama cabang tujuan |
| **No Plat Mobil** | Ketik nomor plat mobil |
| **Jam Muat** | Pilih jam mulai memuat barang |
| **Jam Selesai Muat** | Pilih jam selesai memuat |
| **Jam Berangkat** | Pilih jam mobil berangkat |
| **Nama Supir** | Ketik nama supirnya |
| **Keterangan** | Opsional |

4. Klik **"Simpan"**

---

## 📥 Modul 6: Datang Mobil Supplier

**Dipake oleh:** Checker Terima
**Gunanya:** Mencatat kedatangan mobil supplier

### Cara Input

1. Klik menu **"Datang Mobil Supplier"** di sidebar (grup Penerimaan)
2. Klik tombol **"Tambah"**
3. Isi data: No Plat, Nama Sopir, Nama Supplier, Jam Datang, Jam Selesai Bongkar
4. Klik **"Simpan"**

---

## 📋 Modul 7: Cuti & Absensi

**Dipake oleh:** Admin
**Gunanya:** Melihat dan mencatat cuti/sakit/izin karyawan per bulan

### Cara Input

1. Klik menu **"Cuti & Absensi"** di sidebar (grup Administrasi)
2. Pilih **Bulan** dan **Tahun** untuk lihat matriks
3. Klik tombol **"Input Cuti / Absen"**
4. Pilih karyawan, tanggal, tipe (Cuti/Sakit/Izin), isi keterangan
5. Klik **"Simpan"**

### Cara Hapus

Klik badge **C/S/I** di tabel matriks → konfirmasi → terhapus.

### Aturan

- ✅ Cuti max **12 hari per tahun** (reset tiap tahun)
- ✅ Sakit & Izin **tidak terbatas**
- ✅ Tidak bisa input **tanggal kemarin**
- ❌ Tidak boleh **duplikat tanggal** per karyawan

---

## ⚡ Yang Terisi Otomatis (Tidak Perlu Diisi Manual)

| Data | Keterangan |
|------|------------|
| 🆔 **ID Task** | Dibuat otomatis — contoh: `RET-SUP-00001` (format baru: prefix + 5 digit) |
| 👤 **Checker** | Otomatis sesuai yang login |
| 📅 **Tanggal** | Otomatis hari ini |

> ⚠️ Setiap 1 baris = 1 ID Task (tidak lagi 1 ID untuk banyak baris)

---

## 💡 Ada Kesulitan?

Kalau bingung atau ada error, tanya ke **Admin** atau tim IT.
