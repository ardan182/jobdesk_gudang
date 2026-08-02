<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $fileName }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 7px; }
        h2 { text-align: center; margin-bottom: 8px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 0.5px solid #666; padding: 2px 3px; text-align: center; }
        th { background-color: #f0f0f0; font-weight: bold; }
        td:first-child { text-align: left; font-weight: bold; }
        .sisa { font-weight: bold; }
    </style>
</head>
<body>
    <h2>{{ $fileName }}</h2>
    <table>
        <thead>
            <tr>
                @foreach ($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    @foreach ($row as $i => $cell)
                        <td @class(['sisa' => $i === count($row) - 1])>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
