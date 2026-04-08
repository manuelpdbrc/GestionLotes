<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de Inventario</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
            text-align: center;
            border-bottom: 2px solid #ccc;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .estado-disponible { color: #22c55e; font-weight: bold; }
        .estado-bloqueado { color: #eab308; font-weight: bold; }
        .estado-reservado { color: #1e1e1e; font-weight: bold; }
        .estado-vendido { color: #1e1e1e; font-weight: bold; }
        .estado-no_disponible { color: #999999; font-style: italic; }

    </style>
</head>
<body>

    <div class="header">
        <h1>Estado de Inventario</h1>
        <p>Reporte generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Manzana</th>
                <th>Lote</th>
                <th>Superficie</th>
                <th>Precio (USD)</th>
                <th>Estado</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lots as $lot)
                <tr>
                    <td>{{ $lot->manzana }}</td>
                    <td>{{ $lot->nro_lote }}</td>
                    <td>{{ number_format($lot->superficie, 2) }} m&sup2;</td>
                    <td>${{ number_format($lot->precio, 2) }}</td>
                    <td class="estado-{{ $lot->estado }}">
                        {{ $lot->label }}
                    </td>
                    <td>{{ $lot->observaciones ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
