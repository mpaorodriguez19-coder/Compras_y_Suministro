<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe Transparencia</title>
    <style>
        @page {
            margin: 120px 20px 60px 20px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 90px;
            text-align: center;
        }

        footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 40px;
            border-top: 1px solid #ccc;
            padding-top: 5px;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 3px;
            text-align: center;
        }

        th {
            background-color: #eee;
            text-transform: uppercase;
            font-size: 9px;
        }

        h1 {
            font-size: 16px;
            margin: 0;
        }

        h2 {
            font-size: 13px;
            margin: 5px 0;
        }
    </style>
</head>

<body>

    <script type="text/php">
        if (isset($pdf)) {
            $x = 720; 
            $y = 570;
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $font = null;
            $size = 9;
            $color = array(0,0,0);
            $pdf->page_text($x, $y, $text, $font, $size, $color, 0.0, 0.0, 0.0);
        }
    </script>

    <header>
        <h1>MUNICIPALIDAD DE DANLÍ</h1>
        <h2>INFORME DE TRANSPARENCIA</h2>
        <div>
            <strong>PERÍODO:</strong> {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} AL
            {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
        </div>
    </header>

    <footer>
        <div style="float:left">Generado por: {{ auth()->user()->name ?? 'Sistema' }}</div>
    </footer>

    <main>
        <table>
            <thead>
                <tr>
                    <th width="10%">Fecha</th>
                    <th width="8%">No. Orden</th>
                    <th width="20%">Proveedor</th>
                    <th width="15%">RTN</th>
                    <th width="30%">Concepto / Descripción</th>
                    <th width="10%">Monto</th>
                    <th width="7%">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ordenes as $orden)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($orden->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $orden->numero }}</td>
                        <td style="text-align:left">{{ optional($orden->proveedor)->nombre }}</td>
                        <td>{{ optional($orden->proveedor)->rtn ?? optional($orden->proveedor)->nit }}</td>
                        <td style="text-align:left">
                            {{ \Illuminate\Support\Str::limit($orden->concepto ?: $orden->items->first()->descripcion, 100) }}
                        </td>
                        <td style="text-align:right">L. {{ number_format($orden->total, 2) }}</td>
                        <td>Completado</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>

</html>
