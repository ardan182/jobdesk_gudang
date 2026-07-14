<?php

namespace App\Exports;

use Symfony\Component\HttpFoundation\Response;

class SupplierTemplateExport
{
    public function download(): Response
    {
        $headers = [
            'kode_supplier <span style="color:red">*</span>',
            'nama_supplier <span style="color:red">*</span>',
            'alamat',
            'no_telepon',
            'keterangan',
        ];

        $rows = [
            $headers,
            ['CONTOH-001', 'PT Supplier Contoh', 'Jl. Contoh No. 123', '021-12345678', 'Catatan'],
            ['CONTOH-002', 'CV Distribusi', 'Jl. Raya No. 45', '08123456789', ''],
        ];

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office"
                      xmlns:x="urn:schemas-microsoft-com:office:excel"
                      xmlns="http://www.w3.org/TR/REC-html40">
        <head><meta charset="UTF-8"><title>Template Supplier</title>
        <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>
        <x:Name>Sheet1</x:Name></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
        <style>td { mso-number-format:"\@"; }</style>
        </head><body><table>';

        foreach ($rows as $rowIdx => $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                if ($rowIdx === 0) {
                    $html .= "<th style=\"font-weight:bold;text-align:left\">$cell</th>";
                } else {
                    $val = htmlspecialchars($cell, ENT_QUOTES, 'UTF-8');
                    $html .= "<td>$val</td>";
                }
            }
            $html .= '</tr>';
        }

        $html .= '</table></body></html>';

        return response()->stream(
            fn () => print $html,
            200,
            [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="template-supplier.xls"',
            ]
        );
    }
}
