<?php

namespace App\Services;

use Filament\Forms\Components\CheckboxList;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TableExportService
{
    /**
     * Checkbox list untuk memilih kolom yang akan diexport (label sebagai value).
     *
     * @param  array<string, string>  $columns  label => attribute path
     */
    public static function exportColumnCheckboxList(array $columns): CheckboxList
    {
        return CheckboxList::make('columns')
            ->label('Pilih Kolom yang Diexport')
            ->options(array_combine(array_keys($columns), array_keys($columns)))
            ->default(array_keys($columns))
            ->bulkToggleable()
            ->columns(2);
    }

    /**
     * Filter array kolom (label => path) hanya label yang dipilih.
     *
     * @param  array<string, string>  $columns
     * @param  array<int|string>  $selectedLabels
     * @return array<string, string>
     */
    public static function filterExportColumns(array $columns, array $selectedLabels): array
    {
        return array_filter(
            $columns,
            fn (string $label): bool => in_array($label, $selectedLabels, true),
            ARRAY_FILTER_USE_KEY,
        );
    }
    /**
     * @param  array<string, string>  $columns  label => attribute path (support dot-notation)
     * @param  array<string, callable>  $formatters  attribute path => callable($record): string
     */
    public static function streamXlsx(Builder $query, array $columns, string $fileName, array $formatters = []): StreamedResponse
    {
        return response()->streamDownload(function () use ($query, $columns, $formatters): void {
            $writer = new XlsxWriter;
            $writer->openToFile('php://output');
            $writer->addRow(Row::fromValues(array_keys($columns)));

            $query->chunk(500, function (Collection $records) use ($writer, $columns, $formatters): void {
                foreach ($records as $record) {
                    $row = [];
                    foreach ($columns as $path) {
                        $row[] = isset($formatters[$path])
                            ? $formatters[$path]($record)
                            : self::resolveValue($record, $path);
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
     * @param  array<string, callable>  $formatters  attribute path => callable($record): string
     */
    public static function streamPdf(Builder $query, array $columns, string $fileName, array $formatters = []): StreamedResponse
    {
        return response()->streamDownload(function () use ($query, $columns, $fileName, $formatters): void {
            $rows = $query->limit(200)->get();

            $html = view('exports.table-pdf', [
                'columns' => $columns,
                'rows' => $rows,
                'fileName' => $fileName,
                'formatters' => $formatters,
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

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     */
    public static function streamXlsxFromRows(array $headers, array $rows, string $fileName): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            $writer = new XlsxWriter;
            $writer->openToFile('php://output');

            $border = new Border(
                new BorderPart(Border::LEFT, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
                new BorderPart(Border::RIGHT, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
                new BorderPart(Border::TOP, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
                new BorderPart(Border::BOTTOM, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
            );

            $headerStyle = (new Style)->setBorder($border)->setFontBold();
            $rowStyle = (new Style)->setBorder($border);

            $writer->addRow(Row::fromValues($headers, $headerStyle));
            foreach ($rows as $row) {
                $writer->addRow(Row::fromValues($row, $rowStyle));
            }
            $writer->close();
        }, $fileName.'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     */
    public static function streamPdfFromRows(array $headers, array $rows, string $fileName): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows, $fileName): void {
            $html = view('exports.table-pdf-rows', [
                'headers' => $headers,
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
