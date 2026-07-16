<?php

namespace App\Exports;

use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

class EmployeesExport
{
    public function download(): Response
    {
        $headers = ['Nama Karyawan', 'No WhatsApp', 'Divisi Gudang'];
        $sample = [
            ['Budi Santoso', '08123456789', 'Retur'],
            ['Sari Dewi', '082233445566', 'Pecah Belah'],
            ['Ahmad Hidayat', '083355779911', 'Sariindah'],
        ];
        $divisiList = ['Retur', 'Pecah Belah', 'Sariindah', 'Elektrik', 'CS Gudang', 'Kirim Cabang', 'Umum'];

        $zip = new ZipArchive;
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip->open($tmp, ZipArchive::CREATE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
</Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');

        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets><sheet name="Employee" sheetId="1" r:id="rId1"/></sheets>
</workbook>');

        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>');

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
        $allStrings = array_merge($headers, ['', '-']);
        foreach ($sample as $row) {
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
        $divisiFormula = '"' . implode(',', $divisiList) . '"';
        $wsXml = '<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>';

        // Header row (style 1 = bold)
        $wsXml .= '<row r="1">';
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $idx = array_search($h, $allStrings);
            $wsXml .= '<c r="' . $col . '1" t="s" s="1"><v>' . $idx . '</v></c>';
        }
        $wsXml .= '</row>';

        // Sample data rows
        foreach ($sample as $r => $row) {
            $rowNum = $r + 2;
            $wsXml .= '<row r="' . $rowNum . '">';
            foreach ($row as $c => $val) {
                $col = chr(65 + $c);
                $idx = array_search($val, $allStrings);
                $wsXml .= '<c r="' . $col . $rowNum . '" t="s"><v>' . $idx . '</v></c>';
            }
            $wsXml .= '</row>';
        }

        $wsXml .= '</sheetData>';

        // Data Validation for Divisi column (C)
        $wsXml .= '  <dataValidations count="1">
    <dataValidation type="list" allowBlank="1" showDropDown="0" sqref="C2:C1048576">
      <formula1>' . htmlspecialchars($divisiFormula, ENT_QUOTES, 'UTF-8') . '</formula1>
    </dataValidation>
  </dataValidations>';

        $wsXml .= '</worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $wsXml);

        $zip->close();

        $content = file_get_contents($tmp);
        unlink($tmp);

        return response()->stream(
            fn () => print $content,
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="employee-template.xlsx"',
            ]
        );
    }
}
