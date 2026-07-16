<?php

namespace App\Exports;

use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

class SuppliersExport
{
    public function download(): Response
    {
        $headers = ['kode_supplier *', 'nama_supplier *', 'alamat', 'no_telepon', 'keterangan'];
        $rows = [
            ['CONTOH-001', 'PT Supplier Contoh', 'Jl. Contoh No. 123', '021-12345678', 'Catatan'],
            ['CONTOH-002', 'CV Distribusi', 'Jl. Raya No. 45', '08123456789', ''],
        ];

        $zip = new ZipArchive;
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip->open($tmp, ZipArchive::CREATE);

        // [Content_Types].xml
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
</Types>');

        // _rels/.rels
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');

        // xl/workbook.xml
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets><sheet name="Sheet1" sheetId="1" r:id="rId1"/></sheets>
</workbook>');

        // xl/_rels/workbook.xml.rels
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>');

        // xl/styles.xml (minimal)
        $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="2">
    <font><sz val="11"/><name val="Calibri"/></font>
    <font><b/><sz val="11"/><name val="Calibri"/></font>
  </fonts>
  <fills count="2">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
  </fills>
  <borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>
  <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
  <cellXfs count="2">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>
  </cellXfs>
</styleSheet>');

        // xl/sharedStrings.xml
        $allStrings = [];
        foreach ($headers as $h) {
            $allStrings[] = $h;
        }
        foreach ($rows as $row) {
            foreach ($row as $cell) {
                $allStrings[] = $cell;
            }
        }

        $ssXml = '<?xml version="1.0" encoding="UTF-8"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($allStrings) . '" uniqueCount="' . count($allStrings) . '">';
        foreach ($allStrings as $s) {
            $ssXml .= '<si><t>' . htmlspecialchars($s, ENT_QUOTES, 'UTF-8') . '</t></si>';
        }
        $ssXml .= '</sst>';
        $zip->addFromString('xl/sharedStrings.xml', $ssXml);

        // xl/worksheets/sheet1.xml
        $totalRows = count($rows) + 1;
        $totalCols = count($headers);
        $wsXml = '<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>';

        // Header row (style 1 = bold)
        $wsXml .= '<row r="1">';
        foreach ($headers as $i => $h) {
            $col = $this->colLetter($i + 1);
            $idx = array_search($h, $allStrings);
            $wsXml .= '<c r="' . $col . '1" t="s" s="1"><v>' . $idx . '</v></c>';
        }
        $wsXml .= '</row>';

        // Data rows (style 0 = normal)
        foreach ($rows as $r => $row) {
            $rowNum = $r + 2;
            $wsXml .= '<row r="' . $rowNum . '">';
            foreach ($row as $c => $cell) {
                $col = $this->colLetter($c + 1);
                $idx = array_search($cell, $allStrings);
                $wsXml .= '<c r="' . $col . $rowNum . '" t="s"><v>' . $idx . '</v></c>';
            }
            $wsXml .= '</row>';
        }

        $wsXml .= '</sheetData></worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $wsXml);

        $zip->close();

        $content = file_get_contents($tmp);
        unlink($tmp);

        return response()->stream(
            fn () => print $content,
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="template-supplier.xlsx"',
            ]
        );
    }

    private function colLetter(int $i): string
    {
        return chr(64 + $i);
    }
}
