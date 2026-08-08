<?php

namespace App\Services;

use App\Filament\Support\PermissionMenu;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    /**
     * Label tampilan untuk nilai lama/new pada diff update.
     */
    protected static array $fieldLabels = [
        'name' => 'Nama',
        'email' => 'Email',
        'password' => 'Password',
        'roles' => 'Role',
        'is_super_admin' => 'Super Admin',
        'permission_template' => 'Template Permission',
        'cabang' => 'Cabang',
        'status' => 'Status',
        'keterangan' => 'Keterangan',
        'catatan' => 'Catatan',
        'nomor_sj' => 'Nomor SJ',
        'no_sj_retur' => 'No. SJ Retur',
        'no_surat_jalan' => 'No. Surat Jalan',
        'no_po' => 'No. PO',
        'no_po_referensi' => 'No. PO',
        'nomor_po_referensi' => 'No. PO',
        'total_qty' => 'Total Qty',
        'qty_checker' => 'Qty Check',
        'jumlah_sj_bagus' => 'Jml SJ Bagus',
        'jumlah_sj_jelek' => 'Jml SJ Jelek',
        'catatan_bagus' => 'Catatan Bagus',
        'catatan_jelek' => 'Catatan Jelek',
        'jenis_retur' => 'Jenis Retur',
        'jenis_pengiriman' => 'Jenis Pengiriman',
        'jenis_kiriman' => 'Jenis Kiriman',
        'jenis_absen' => 'Jenis Absen/Cuti',
        'jenis' => 'Jenis Dokumen',
        'tanggal_datang' => 'Tanggal Datang',
        'tanggal_buat' => 'Tanggal Buat',
        'tanggal_kirim' => 'Tanggal Kirim',
        'tanggal_bongkar' => 'Tanggal Bongkar',
        'tanggal_mulai' => 'Tanggal Mulai',
        'tanggal_selesai' => 'Tanggal Selesai',
        'tanggal_terbit' => 'Tanggal Terbit',
        'jam_datang' => 'Jam Datang',
        'jam_bongkar' => 'Jam Bongkar',
        'jam_muat' => 'Jam Muat',
        'jam_selesai_muat' => 'Jam Selesai Muat',
        'jam_berangkat' => 'Jam Berangkat',
        'jam_tiba' => 'Jam Tiba',
        'jam_kedatangan' => 'Jam Kedatangan',
        'jangka_waktu' => '',
        'nama_supplier' => 'Supplier',
        'nama_supplier_ekspedisi' => 'Supplier/Ekspedisi',
        'nama_ekspedisi' => 'Ekspedisi',
        'nama_sopir' => 'Sopir',
        'nama_supir' => 'Sopir',
        'no_plat_mobil' => 'No. Plat',
        'no_telepon' => 'No. Telepon',
        'no_whatsapp' => 'No. WhatsApp',
        'no_nota_retur' => 'No. Nota Retur',
        'jumlah_kolian' => 'Jumlah Kolian',
        'jumlah_koli' => 'Jumlah Koli',
        'jumlah_faktur' => 'Jumlah Faktur',
        'lembar_sj' => 'Lembar SJ',
        'total_koli_keluar' => 'Total Koli Keluar',
        'total_kolian_masuk' => 'Total Kolian Masuk',
        'diserahkan_kepada' => 'Diserahkan Kepada',
        'helper' => 'Helper',
        'masa_berlaku' => 'Masa Berlaku',
        'periode' => 'Periode',
        'nomor_dokumen' => 'Nomor Dokumen',
        'nama_dokumen' => 'Nama Dokumen',
        'kategori' => 'Kategori',
        'versi' => 'Versi',
        'deskripsi' => 'Deskripsi',
        'nama_toko' => 'Toko',
        'alamat' => 'Alamat',
        'kode_supplier' => 'Kode Supplier',
        'nama_karyawan' => 'Nama Karyawan',
        'nomor_polisi' => 'Nomor Polisi',
        'nama_barang' => 'Nama Barang',
        'kondisi_barang' => 'Kondisi Barang',
        'penyelesaian' => 'Penyelesaian',
        'status_input' => 'Status Input',
    ];

    public static function log(Model|string $model, string $module, string $action, string $description, ?string $reference = null, ?int $userId = null): void
    {
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            return;
        }

        $userId ??= auth()->id() ?? (is_object($model) ? $model->user_id : null);

        if (! $userId) {
            return;
        }

        ActivityLog::create([
            'user_id' => $userId,
            'module' => $module,
            'id_task' => is_object($model) ? ($model->id_task ?? '-') : $model,
            'description' => $description,
            'reference' => $reference,
            'action' => $action,
        ]);
    }

    public static function created(Model $model, string $module, string $description, ?string $reference = null): void
    {
        self::log($model, $module, 'create', $description, $reference);
    }

    public static function updated(Model $model, string $module, array $changes, ?string $reference = null): void
    {
        if (empty($changes)) {
            return;
        }
        self::log($model, $module, 'update', 'Diubah: ' . implode('; ', $changes), $reference);
    }

    public static function deleted(Model $model, string $module, string $description, ?string $reference = null): void
    {
        self::log($model, $module, 'delete', 'Dihapus: ' . $description, $reference);
    }

    /**
     * Daun diff update: [ "Label: old → new", ... ]
     */
    public static function changes(Model $model, array $tracked): array
    {
        $changes = [];
        foreach ($tracked as $field) {
            $old = self::stringify($model->getOriginal($field));
            $new = self::stringify($model->$field);
            if ($old !== $new && ! (blank($old) && blank($new))) {
                $changes[] = self::fieldLabel($field) . ': ' . ($old ?: '-') . ' → ' . ($new ?: '-');
            }
        }
        return $changes;
    }

    public static function fieldLabel(string $field): string
    {
        $label = self::$fieldLabels[$field] ?? str_replace('_', ' ', ucwords($field, '_'));
        return trim(ucfirst($label));
    }

    /**
     * Label modul sesuai menu (key PermissionMenu), fallback label lama/asli.
     */
    public static function moduleLabel(string $module): string
    {
        foreach (PermissionMenu::groups() as $group => $modules) {
            foreach ($modules as $m) {
                if ($m['key'] === $module) {
                    return $m['label'];
                }
            }
        }

        return self::$moduleLabels[$module] ?? $module;
    }

    protected static array $moduleLabels = [
        'roles' => 'Roles',
    ];

    /**
     * Rapikan deskripsi lawas (data lama yang masih memakai field Inggris).
     */
    public static function prettifyDescription(string $description): string
    {
        foreach (self::$fieldLabels as $field => $label) {
            $description = str_replace($field . ':', $label . ':', $description);
        }
        $description = str_replace('—', ';', $description);
        return trim($description);
    }

    protected static function stringify(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        if (is_bool($value)) {
            return $value ? 'Ya' : 'Tidak';
        }
        if (is_array($value)) {
            return implode(', ', array_map(fn ($v) => is_scalar($v) ? (string) $v : (string) json_encode($v), $value));
        }
        if (is_object($value)) {
            return (string) $value;
        }
        return (string) $value;
    }
}