<?php

namespace App\Imports;

use App\Models\Supplier;
use DOMDocument;
use ZipArchive;

class SupplierImport
{
    public function import(string $filePath): array
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($ext === 'csv') {
            return $this->importCsv($filePath);
        }

        if ($ext === 'xlsx') {
            return $this->importXlsx($filePath);
        }

        if ($ext === 'xls') {
            return $this->importXls($filePath);
        }

        return ['success' => 0, 'skipped' => 0, 'error' => 'Format file tidak didukung. Gunakan .xlsx atau .csv.'];
    }

    private function importCsv(string $filePath): array
    {
        $file = fopen($filePath, 'r');
        $headers = array_map([$this, 'sanitizeHeader'], fgetcsv($file) ?: []);
        $expected = ['kode_supplier', 'nama_supplier', 'alamat', 'no_telepon', 'keterangan'];

        if ($headers !== $expected) {
            fclose($file);
            return ['success' => 0, 'skipped' => 0, 'error' => 'Header tidak sesuai. Gunakan template yang disediakan.'];
        }

        $result = ['success' => 0, 'skipped' => 0, 'error' => null];

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($expected, $row);
            $this->processRow($data, $result);
        }

        fclose($file);
        return $result;
    }

    private function importXlsx(string $filePath): array
    {
        $zip = new ZipArchive;
        if ($zip->open($filePath) !== true) {
            return ['success' => 0, 'skipped' => 0, 'error' => 'Tidak bisa membuka file XLSX.'];
        }

        $sharedStrings = [];
        $ssXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($ssXml) {
            $ss = simplexml_load_string($ssXml);
            foreach ($ss->si as $si) {
                $sharedStrings[] = (string) $si->t;
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if (!$sheetXml) {
            return ['success' => 0, 'skipped' => 0, 'error' => 'Tidak menemukan sheet dalam file XLSX.'];
        }

        $sheet = simplexml_load_string($sheetXml);
        $rows = $sheet->sheetData->row ?? [];

        $expected = ['kode_supplier', 'nama_supplier', 'alamat', 'no_telepon', 'keterangan'];
        $result = ['success' => 0, 'skipped' => 0, 'error' => null];
        $isFirstRow = true;

        foreach ($rows as $row) {
            $cells = [];
            foreach ($row->c as $cell) {
                $col = preg_replace('/[0-9]/', '', (string) $cell['r']);
                $value = '';

                if ((string) $cell['t'] === 's') {
                    $idx = (int) (string) $cell->v;
                    $value = $sharedStrings[$idx] ?? '';
                } else {
                    $value = (string) $cell->v;
                }

                $cells[$col] = $value;
            }

            if ($isFirstRow) {
                $isFirstRow = false;
                $headers = array_map([$this, 'sanitizeHeader'], array_values($cells));
                if ($headers !== $expected) {
                    return ['success' => 0, 'skipped' => 0, 'error' => 'Header tidak sesuai. Gunakan template yang disediakan.'];
                }
                continue;
            }

            $data = [];
            foreach ($expected as $i => $key) {
                $colLetter = chr(65 + $i);
                $data[$key] = $cells[$colLetter] ?? '';
            }

            $this->processRow($data, $result);
        }

        return $result;
    }

    private function importXls(string $filePath): array
    {
        $html = file_get_contents($filePath);
        if (!$html) {
            return ['success' => 0, 'skipped' => 0, 'error' => 'Tidak bisa membaca file.'];
        }

        libxml_use_internal_errors(true);
        $doc = new DOMDocument;
        $doc->loadHTML($html);
        libxml_clear_errors();

        $tables = $doc->getElementsByTagName('table');
        if ($tables->length === 0) {
            return ['success' => 0, 'skipped' => 0, 'error' => 'Tidak menemukan tabel dalam file.'];
        }

        $rows = $tables->item(0)->getElementsByTagName('tr');
        $expected = ['kode_supplier', 'nama_supplier', 'alamat', 'no_telepon', 'keterangan'];
        $result = ['success' => 0, 'skipped' => 0, 'error' => null];
        $isFirstRow = true;

        foreach ($rows as $tr) {
            $cells = [];
            foreach ($tr->getElementsByTagName('th') as $th) {
                $cells[] = $this->sanitizeHeader($th->textContent);
            }
            foreach ($tr->getElementsByTagName('td') as $td) {
                $cells[] = $td->textContent;
            }

            if (empty($cells)) {
                continue;
            }

            if ($isFirstRow) {
                $isFirstRow = false;
                $headers = array_map('trim', $cells);
                if ($headers !== $expected) {
                    return ['success' => 0, 'skipped' => 0, 'error' => 'Header tidak sesuai. Gunakan template yang disediakan.'];
                }
                continue;
            }

            $data = array_combine($expected, $cells);
            $this->processRow($data, $result);
        }

        return $result;
    }

    private function sanitizeHeader(string $header): string
    {
        return trim(str_replace('*', '', strip_tags($header)));
    }

    private function processRow(array $data, array &$result): void
    {
        $data['kode_supplier'] = trim($data['kode_supplier'] ?? '');
        $data['nama_supplier'] = trim($data['nama_supplier'] ?? '');

        if (empty($data['kode_supplier']) || empty($data['nama_supplier'])) {
            return;
        }

        if (Supplier::where('kode_supplier', $data['kode_supplier'])->exists()) {
            $result['skipped']++;
            return;
        }

        Supplier::create([
            'kode_supplier' => strtoupper($data['kode_supplier']),
            'nama_supplier' => $data['nama_supplier'],
            'alamat' => $data['alamat'] ?? null,
            'no_telepon' => $data['no_telepon'] ?? null,
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        $result['success']++;
    }
}
