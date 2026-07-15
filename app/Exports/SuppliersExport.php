<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class SuppliersExport extends DefaultValueBinder implements FromArray, WithHeadings, WithCustomValueBinder
{
    public function headings(): array
    {
        return [
            'kode_supplier *',
            'nama_supplier *',
            'alamat',
            'no_telepon',
            'keterangan',
        ];
    }

    public function array(): array
    {
        return [
            ['CONTOH-001', 'PT Supplier Contoh', 'Jl. Contoh No. 123', '021-12345678', 'Catatan'],
            ['CONTOH-002', 'CV Distribusi', 'Jl. Raya No. 45', '08123456789', ''],
        ];
    }
}
