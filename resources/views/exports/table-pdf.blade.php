<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $fileName }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        h2 { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #666; padding: 4px 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        tr:nth-child(even) td { background-color: #fafafa; }
    </style>
</head>
<body>
    <h2>{{ $fileName }}</h2>
    <table>
        <thead>
            <tr>
                @foreach (array_keys($columns) as $label)
                    <th>{{ $label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $record)
                <tr>
                    @foreach ($columns as $path)
                        <td>{{ isset($formatters[$path]) ? $formatters[$path]($record) : \App\Services\TableExportService::resolveValue($record, $path) }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
