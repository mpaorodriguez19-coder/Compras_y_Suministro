<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Resumen por Proveedor</title>
    <style>
        @page {
            margin: 120px 40px 60px 40px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
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
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #eee;
            text-transform: uppercase;
        }

        h1 {
            font-size: 16px;
            margin: 0;
        }

        h2 {
            font-size: 14px;
            margin: 5px 0;
        }
    </style>
</head>

<body>

    <script type="text/php">
        if (isset($pdf)) {
            $x = 520;
            $y = 750;
            $text = "Pág {PAGE_NUM} - {PAGE_COUNT}";
            $font = null;
            $size = 9;
            $color = array(0,0,0);
            $pdf->page_text($x, $y, $text, $font, $size, $color, 0.0, 0.0, 0.0);
        }
    </script>

    <header
        style="position: fixed; top: -100px; left: 0; right: 0; height: 90px; display: flex; align-items: center; justify-content: center; padding: 0 10px;">

        <!-- Logo izquierdo -->
        <div style="position: absolute; left: 0; top: 50%; transform: translateY(-50%);">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('imagenes/logo_izq.png'))) }}"
                style="width:70px; height:auto;" alt="Logo Izquierdo">
        </div>

        <!-- Texto central -->
        <div style="text-align: center;">
            <h1 style="margin:0; font-size:16pt;">MUNICIPALIDAD DE DANLÍ</h1>
            <h2 style="margin:0; font-size:14pt;">RESUMEN TOTAL POR PROVEEDOR</h2>
            <div style="margin-top:5px; font-size:12pt;">
                <strong>PERÍODO:</strong> {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} AL
                {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
            </div>
        </div>

        <!-- Logo derecho -->
        <div style="position: absolute; right: 0; top: 50%; transform: translateY(-50%);">
            <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('imagenes/logo_der.jpeg'))) }}"
                style="width:70px; height:auto;" alt="Logo Derecho">
        </div>

    </header>


    <footer>
        <div style="float:left">Generado por: {{ auth()->user()->name ?? 'Sistema' }}</div>
    </footer>

    <main>
        <table>
            <thead>
                <tr>
                    <th>Proveedor</th>
                    <th>RTN</th>
                    <th>Cant. Órdenes</th>
                    <th>Total Acumulado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($proveedores as $proveedor)
                    <tr>
                        <td style="text-align:left">{{ $proveedor->nombre }}</td>
                        <td>{{ $proveedor->rtn ?? 'N/A' }}</td>
                        <td>{{ $proveedor->ordenes_count ?? $proveedor->ordenes->count() }}</td>
                        <td style="text-align:right; font-weight:bold;">L.
                            {{ number_format($proveedor->ordenes_sum_total ?? $proveedor->ordenes->sum('total'), 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>

</html>
