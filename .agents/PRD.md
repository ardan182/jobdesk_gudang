# PRD: Jobdesk Gudang AP

## BAGIAN 1: Visi, Tujuan & Key Metrics

### Visi Produk
**Jobdesk Gudang AP** adalah platform web untuk mendigitalisasi management jobdesk harian di gudang. Menyatukan semua checker — retur, terima barang, keluar barang, dan kiriman cabang — dalam satu dashboard real-time, sehingga operasional gudang lebih terintegrasi, transparan, dan bebas dari catatan manual.

### Tujuan Utama
1. **Digitalisasi jobdesk harian** — Ganti buku catatan/Excel dengan form digital terstruktur.
2. **Integrasi antar checker** — Semua data task dari setiap checker tersentral di Admin.
3. **Monitoring real-time** — Admin & checker lihat status task langsung via dashboard card.
4. **Laporan cepat & akurat** — Table grid dengan pagination, sort, filter tanggal, dan search.
5. **Zero error data** — ID_TASK & NO BARIS otomatis, dropdown terstandarisasi.

### Key Metrics / OKR
1. **Objective:** Semua task harian tercatat digital
   - **KR 1:** 100% checker input task setiap hari via sistem
   - **KR 2:** 0% penggunaan catatan manual dalam 2 minggu
2. **Objective:** Integrasi data antar checker
   - **KR 1:** Semua role bisa akses dashboard sesuai hak akses
   - **KR 2:** Admin bisa lihat rekap semua checker dalam 1 halaman

### Value Proposition
- Semua data jobdesk gudang dalam satu platform
- Role-based access (Admin, Checker Retur, Checker Terima, Checker Keluar, Checker Kiriman)
- Form repeater input massal untuk efisiensi input
- Laporan interaktif tanpa perlu Excel

---

## BAGIAN 2: User Persona

### Persona 1: Pak Budi (Admin Gudang)
- **Usia/Pekerjaan:** 35 tahun, Kepala Gudang / Admin
- **Level Teknis:** Menengah (familiar komputer, browser, Excel)
- **Tujuan:** Memantau seluruh aktivitas checker gudang dari satu dashboard, membuat user & mengelola akses
- **Pain Points:** Harus kumpulin laporan dari 4+ checker manual, data sering telat atau tidak konsisten, susah rekap harian
- **Motivasi:** Ingin satu layar yang langsung nunjukin semua status jobdesk tanpa harus tanya satu-satu

### Persona 2: Rina (Checker Retur)
- **Usia/Pekerjaan:** 28 tahun, Staff Checker Retur Gudang
- **Level Teknis:** Pemula (biasa pakai HP, jarang pakai komputer)
- **Tujuan:** Input data kirim retur ke supplier & terima retur dari cabang dengan cepat dan akurat
- **Pain Points:** Nulis manual di buku, sering ketuker data, baris susah diurutin, lupa catat jam muat
- **Motivasi:** Form digital yang tinggal isi dropdown & time picker, ga perlu nulis ulang

### Persona 3: Agus (Checker Terima/Keluar/Kiriman)
- **Usia/Pekerjaan:** 30 tahun, Staff Checker Gudang (rotasi antar pos)
- **Level Teknis:** Pemula-Menengah
- **Tujuan:** Input task harian sesuai pos checker-nya (terima barang supplier, keluar barang ke toko, atau kiriman per mobil)
- **Pain Points:** Beda pos beda format catatan, data tidak sinkron dengan admin, susah cari data lama kalau ada komplain
- **Motivasi:** Satu akun login langsung ke menu sesuai role, bisa cari data lewat laporan yang bisa di-search & filter tanggal

---

## BAGIAN 3: User Flow / Journey

### Alur Utama (Main Flow)
1. **[Login]** → Admin/Checker login dengan email & password sesuai role
2. **[Dashboard]** → Tampil dashboard card sesuai role: card jobdesk all (Admin) atau jobdesk spesifik (Checker)
3. **[Menu Task]** → Checker pilih menu input task (Retur, Terima, Keluar, atau Kiriman)
4. **[Input Task]** → Isi form repeater (multi-row), ID_TASK & NO BARIS otomatis, dropdown & time picker siap
5. **[Simpan]** → Data tersimpan real-time, dashboard card update status
6. **[Laporan]** → Admin/Checker buka laporan → table grid with pagination, sort, filter tanggal, search
7. **[Selesai]** → Semua data terintegrasi, admin pantau dari dashboard

### Alur Alternatif
- **[Checker ganti role]:** Admin bisa set role user; setelah login ulang, checker lihat menu sesuai role barunya
- **[Input banyak baris]:** Form repeater memungkinkan input beberapa baris task sekaligus, lalu submit sekali

### Alur Error
- **[Data tidak valid]:** Validasi form (required fields, format) tampilkan error per baris di repeater
- **[Koneksi putus]:** Sistem kasih notifikasi "Data tersimpan lokal" (bonus / future)
- **[Login gagal]:** 5x gagal = akun terblokir 15 menit (default Filament)

---

## BAGIAN 4: User Stories

**Format:** `Sebagai [peran], saya ingin [tindakan], agar [manfaat].`

### Modul 1: Autentikasi & Role
1. Sebagai Admin, saya ingin membuat user dengan password & role, agar checker punya akses sesuai tugasnya.
2. Sebagai checker, saya ingin login dengan akun yang diberikan admin, agar bisa mengakses menu sesuai role saya.
3. Sebagai Admin, saya ingin mengubah role/password user, bila ada mutasi staff.

### Modul 2: Checker Retur
4. Sebagai checker retur, saya ingin input task kirim retur ke supplier via repeater form (ID & no baris otomatis), agar data retur tercatat digital.
5. Sebagai checker retur, saya ingin input task terima retur dari cabang, agar retur masuk tercatat rapi.
6. Sebagai checker retur, saya ingin melihat dashboard card jumlah task & status hari ini, agar tahu progres kerja.

### Modul 3: Checker Terima Barang Supplier
7. Sebagai checker terima, saya ingin input task terima barang dari supplier (form repeater), agar barang masuk tercatat.

### Modul 4: Checker Keluar Barang
8. Sebagai checker keluar, saya ingin input task keluar barang ke toko/cabang, agar barang keluar terdokumentasi.

### Modul 5: Checker Kiriman Cabang Per Mobil
9. Sebagai checker kiriman, saya ingin input task kiriman per mobil ke cabang (lengkap dengan jam muat, jam selesai, jam berangkat), agar perjalanan mobil tercatat.

### Modul 6: Admin
10. Sebagai Admin, saya ingin melihat dashboard card all user, agar bisa pantau aktivitas seluruh checker.
11. Sebagai Admin, saya ingin CRUD semua menu checker (sebagai backup input), agar bisa bantu jika checker absen.
12. Sebagai Admin, saya ingin melihat laporan table grid dari semua checker dengan pagination, sort, filter tanggal & search, agar rekap harian cepat.
13. Sebagai checker & admin, saya ingin laporan bisa difilter berdasarkan rentang tanggal, agar mudah cari data historis.

---

## BAGIAN 5: Functional Requirements

### Legend Prioritas
- **🔴 Must Have** — Wajib ada di V1
- **🟡 Should Have** — Penting, bisa rilis di patch
- **🟢 Nice to Have** — Bonus, jika waktu cukup

### Modul 0: Autentikasi & Role Management

**FR-01: Login dengan role**
- **Prioritas:** 🔴 Must Have
- **Input:** Email, password (dibuat oleh Admin)
- **Proses:** Validasi kredensial, cek role, redirect ke dashboard sesuai role
- **Output:** Dashboard sesuai hak akses
- **Aturan:** Filament auth bawaan

**FR-02: CRUD User & Role**
- **Prioritas:** 🔴 Must Have
- **Input:** Nama, email, password, role (Admin, Checker Retur, Checker Terima, Checker Keluar, Checker Kiriman)
- **Proses:** Buat/edit/hapus user via panel Admin (Filament User Resource)
- **Output:** User tersimpan, checker bisa login sesuai role

### Modul 1: Dashboard

**FR-03: Dashboard Card Admin**
- **Prioritas:** 🔴 Must Have
- **Input:** — (aggregasi data semua checker)
- **Proses:** Hitung jumlah task per checker hari ini, tampilkan dalam card dengan status
- **Output:** Card dashboard per checker dengan count & status
- **Aturan:** Data real-time per hari ini

**FR-04: Dashboard Card Checker**
- **Prioritas:** 🔴 Must Have
- **Input:** — (aggregasi data task checker yg login)
- **Proses:** Hitung jumlah task checker hari ini, tampilkan card + status
- **Output:** Card dashboard personal

### Modul 2: Checker Retur

**FR-05: Input Task Kirim Retur ke Supplier**
- **Prioritas:** 🔴 Must Have
- **Input:** ID_TASK (auto), NO BARIS (auto), Nama Supplier/Ekspedisi, No Plat Mobil, Nama Sopir, Jam Muat (time picker), Jumlah Kolian, Admin SJ Retur, Status (dropdown: Servis/Tukar/Pot Nota), Keterangan
- **Proses:** Form repeater, simpan ke DB
- **Output:** Task tersimpan, dashboard card update
- **Aturan:** 1 ID_TASK per batch submit (semua baris dalam 1 input ID-nya sama). Format `RET-SUP-YYYYMMDD-XXX`. NO BARIS auto-increment per baris per tanggal. `id_task` INDEX (bukan UNIQUE) karena bisa duplikat antar baris.

**FR-06: Input Task Terima Retur dari Cabang**
- **Prioritas:** 🔴 Must Have
- **Input:** ID_TASK (auto), NO BARIS (auto), Cabang, Jenis Retur (dropdown: Retur Jelek/Retur Bagus), No SJ Retur Indri/ERP, Total Kolian, Jam Bongkar (time picker), Nama Sopir (Select dari Master Sopir), Keterangan
- **Proses:** Form repeater, simpan ke DB
- **Output:** Task tersimpan, dashboard card update
- **Aturan:** 1 ID_TASK per batch submit. Format `RET-CAB-YYYYMMDD-XXX`. `id_task` INDEX.

### Modul 3: Checker Terima Barang dari Supplier

**FR-07: Input Task Terima dari Supplier**
- **Prioritas:** 🔴 Must Have
- **Input:** ID_TASK (auto), NO BARIS (auto), Nama Supplier/Ekspedisi, No PO Referensi, Jumlah Kolian, Jam Bongkar (time picker), Nama Sopir, Status (dropdown: Komplit/Kurang/Lebih), Keterangan
- **Proses:** Form repeater, simpan ke DB
- **Output:** Task tersimpan
- **Aturan:** 1 ID_TASK per batch submit. Format `TRM-SUP-YYYYMMDD-XXX`. `id_task` INDEX.

### Modul 4: Checker Keluar Barang dari Gudang

**FR-08: Input Task Keluar Barang ke Toko/Cabang**
- **Prioritas:** 🔴 Must Have
- **Input:** ID_TASK (auto), NO BARIS (auto), Toko Tujuan (dropdown: Pusat/Ujungberung/Soreang/Majalaya/Cicaheum/Barokah), Supplier, No Referensi SJ, Jumlah Kolian, Jam Naik (time picker), Nama Koordinator, Status (dropdown: Komplit/Kurang/Lebih), Keterangan
- **Proses:** Form repeater, simpan ke DB
- **Output:** Task tersimpan
- **Aturan:** 1 ID_TASK per batch submit. Format `KLR-YYYYMMDD-XXX`. `id_task` INDEX.

### Modul 5: Checker Kiriman Cabang Per Mobil

**FR-09: Input Task Kiriman Per Mobil ke Cabang**
- **Prioritas:** 🔴 Must Have
- **Input:** ID_TASK (auto), NO BARIS (auto), Cabang, No Plat Mobil, Jam Muat, Jam Selesai Muat, Jam Berangkat (time picker), Nama Supir (Select dari Master Sopir), Keterangan
- **Proses:** Form repeater, simpan ke DB
- **Output:** Task tersimpan
- **Aturan:** 1 ID_TASK per batch submit. Format `KRM-YYYYMMDD-XXX`. `id_task` INDEX.

### Modul 6: Admin Panel (All Menu)

**FR-10: Admin CRUD All Checker Task**
- **Prioritas:** 🟡 Should Have
- **Input:** Akses ke semua resource checker
- **Proses:** Admin bisa create/edit/delete task di semua menu checker
- **Output:** Admin bisa backup input jika checker absen

### Modul 7: Laporan

**FR-11: Laporan Table Grid per Menu**
- **Prioritas:** 🔴 Must Have
- **Input:** Filter tanggal, search keywords
- **Proses:** Tampilkan data dalam table (pagination, sortable, filter tanggal, search)
- **Output:** Table data yang bisa di-export (opsional)
- **Aturan:** Setiap checker liat laporan menu-nya sendiri; admin liat semua

**FR-12: Filament Repeater Form**
- **Prioritas:** 🔴 Must Have
- **Input:** Multiple rows data
- **Proses:** Menggunakan Filament Repeater component untuk input multi-row
- **Output:** Semua baris tersimpan dalam satu submit

### Modul 8: Master Sopir

**FR-13: CRUD Master Sopir**
- **Prioritas:** 🔴 Must Have
- **Input:** Nama Sopir
- **Proses:** Admin mengelola daftar sopir (CRUD) via modal di halaman List
- **Output:** Data sopir tersimpan di master_sopirs, muncul sebagai Select dropdown di form Kiriman Mobil & Retur dari Cabang
- **Aturan:** Hanya Admin yang bisa akses. Navigasi grup "Master" setelah Dashboard. Input via modal (bukan halaman terpisah).

---

## BAGIAN 6: Business Model & Monetization

### Model Bisnis
**Gratis (Free)** — Aplikasi internal untuk operasional gudang, tidak dijual ke publik.

### Biaya Operasional
- Hosting: VPS / shared hosting (Laravel + MySQL)
- Domain (jika perlu)
- Maintenance: Dev opsional

### Alur Pendapatan
Tidak ada — ini aplikasi internal perusahaan untuk efisiensi operasional gudang.

### Pertimbangan Bisnis
- Customer Acquisition: 0 (internal team)
- Retention: Dengan semua data terpusat, tim gudang akan ketergantungan (lock-in positif)
- Unit Economics: Tidak relevan (internal tool)
- ROI: Efisiensi waktu admin & checker, penghematan dari tidak perlu software mahal

---

## BAGIAN 7: Non-Functional Requirements

### Performa
- Waktu muat halaman dashboard < 3 detik
- API response < 500ms (via Livewire/Filament)
- Support 10+ user concurrent (staff gudang internal)
- Query laporan dengan filter tanggal selesai < 2 detik

### Keamanan
- Autentikasi via Laravel Filament Shield (role-based)
- Setiap checker hanya bisa lihat menu sesuai role-nya
- Admin bisa akses semua menu
- HTTPS wajib jika di-hosting

### Skalabilitas
- Target 20 user aktif (tim gudang + admin)
- Data task harian: ~100-200 baris/hari
- Database MySQL ready untuk 1+ tahun data

### Usability
- UI menggunakan Filament Panel (bawaan sudah responsive)
- Tampilan mobile-friendly (checker bisa input via HP/tablet)
- Bahasa Indonesia pada label & dropdown
- Form repeater untuk input cepat multi-baris

---

## BAGIAN 8: Out of Scope, Dependensi & Constraints

### Out of Scope (Tidak Dikerjakan di V1)
- Export Excel/PDF — ditunda ke v1.1
- Notifikasi (email/WA) — ditunda ke v2
- Multi-gudang — hanya 1 gudang dulu
- Mobile native app — cukup web responsive via Filament
- Dashboard grafik/chart — hanya card count di V1
- Auto-backup database — backup manual via hosting panel

### Dependensi
- **Laravel 13** — backend framework
- **Filament v5** — admin panel & UI generator
- **Filament Nord Theme** (fork `ardan182/filament-nord-theme`) — arctic north color scheme
- **MySQL / MariaDB** — database
- **Spatie Laravel Permission** — role & permission management
- **Master Sopir** — data sopir terpusat, dropdown di form task
- **Master Mobil** — data mobil (nama & plat) terpusat, dropdown di form Kiriman Mobil
- **Master Toko** — data toko/cabang terpusat, dropdown di form Kiriman Mobil, Keluar Barang, Retur Cabang
- **Composer / NPM** — dependency manager

### Asumsi
- User checker punya akses internet / WiFi gudang
- User checker bisa menggunakan browser (Chrome/HP)
- Satu gudang, satu tim checker tetap
- ID_TASK per batch submit (semua baris dalam 1 input ID-nya sama), NO BARIS auto-increment per baris
- `id_task` pakai INDEX (bukan UNIQUE constraint) karena dalam 1 batch bisa duplikat antar baris

### Constraints
- **Teknis:** Budget hosting minimal (shared hosting sudah cukup)
- **Waktu:** MVP dalam 1-2 minggu (prioritas fitur Must Have)
- **Regulasi:** Tidak ada regulasi khusus untuk internal tool
