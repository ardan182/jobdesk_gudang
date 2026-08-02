<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TableExportService
{
    /**
     * @param  array<string, string>  $columns  label => attribute path (support dot-notation)
     */
    public static function streamXlsx(Builder $query, array $columns, string $fileName): StreamedResponse
    {
        return response()->streamDownload(function () use ($query, $columns): void {
            $writer = new XlsxWriter;
            $writer->openToFile('php://output');
            $writer->addRow(Row::fromValues(array_keys($columns)));

            $query->chunk(500, function (Collection $records) use ($writer, $columns): void {
                foreach ($records as $record) {
                    $row = [];
                    foreach ($columns as $path) {
                        $row[] = self::resolveValue($record, $path);
                    }
                    $writer->addRow(Row::fromValues($row));
                }
            });

            $writer->close();
        }, $fileName.'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @param  array<string, string>  $columns  label => attribute path (support dot-notation)
     */
    public static function streamPdf(Builder $query, array $columns, string $fileName): StreamedResponse
    {
        return response()->streamDownload(function () use ($query, $columns, $fileName): void {
            $rows = $query->limit(200)->get();

            $html = view('exports.table-pdf', [
                'columns' => $columns,
                'rows' => $rows,
                'fileName' => $fileName,
            ])->render();

            $dompdf = new \Dompdf\Dompdf;
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            echo $dompdf->output();
        }, $fileName.'.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public static function resolveValue($record, string $path): string
    {
        $value = data_get($record, $path);

        if ($value instanceof \Carbon\CarbonInterface) {
            $hasTime = $value->format('H:i:s') !== '00:00:00';

            return $value->format($hasTime ? 'd/m/Y H:i' : 'd/m/Y');
        }

        if (is_array($value)) {
            return implode(', ', $value);
        }

        if (is_bool($value)) {
            return $value ? 'Ya' : 'Tidak';
        }

        return (string) ($value ?? '');
    }
}
