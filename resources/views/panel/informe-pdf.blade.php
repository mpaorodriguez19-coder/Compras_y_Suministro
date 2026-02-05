<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Listado de Orden de Compra</title>
    <style>
        @page {
            margin: 140px 30px 60px 30px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin-top: 30px;
        }

        header {
            position: fixed;
            top: -120px;
            left: 0;
            right: 0;
            height: 110px;
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

        /* Table Style */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 5px;
        }

        .header-table td {
            border: none;
            vertical-align: top;
            padding: 0;
        }

        .logo {
            width: 80px;
        }

        .centro {
            text-align: center;
            padding-top: 10px;
        }

        .centro h1 {
            font-size: 18px;
            margin: 2px;
        }

        .centro h2 {
            font-size: 14px;
            margin: 2px;
        }

        .periodo {
            font-weight: bold;
            margin-top: 5px;
            font-size: 12px;
        }

        .info-der {
            text-align: right;
            font-size: 11px;
        }

        .linea {
            border-bottom: 2px solid black;
            margin-bottom: 10px;
        }

        /* Data Table */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data-table th,
        table.data-table td {
            border: none;
            padding: 5px;
            text-align: center;
        }

        table.data-table th {
            border-bottom: 1px solid black;
            font-weight: bold;
            text-transform: uppercase;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        /* Total Row logic like web view */
        .total-row td {
            border-top: 2px solid black;
            font-weight: bold;
            padding-top: 10px;
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

    <header>
        <table class="header-table">
            <tr>
                <td width="20%"><img
                        src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('imagenes/logo_izq.png'))) }}"
                        class="logo"></td>
                <td width="60%" class="centro">
                    <h1>MUNICIPALIDAD DE DANLÍ, EL PARAÍSO</h1>
                    <h2>LISTADO DE ORDEN DE COMPRA</h2>
                    <div class="periodo">
                        PERÍODO DEL: {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}
                        AL {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
                    </div>
                </td>
                <td width="20%" class="info-der">
                    <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('imagenes/logo_der.jpeg'))) }}"
                        class="logo"><br><br>
                    Fecha: {{ date('d/m/Y') }}<br>
                    Página:
                </td>
            </tr>
        </table>
        <div class="linea"></div>
    </header>

    <footer>
        <div style="float:left">Generado por: {{ auth()->user()->name ?? 'Sistema' }}</div>
    </footer>

    <main>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">Nº</th>
                    <th width="10%">Fecha</th>
                    <th width="10%">Numero F.</th>
                    <th width="35%">Proveedor</th>
                    <th width="25%">Solicitada</th>
                    <th width="15%">Valor L.</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $contador = 1;
                    $total = 0;
                @endphp
                @foreach ($ordenes as $orden)
                    @php $total += $orden->total; @endphp
                    <tr>
                        <td>{{ $contador++ }}</td>
                        <td class="text-left">{{ \Carbon\Carbon::parse($orden->fecha)->format('d/m/Y') }}</td>
                        <td class="text-left">{{ $orden->numero }}</td>
                        <td class="text-left">{{ optional($orden->proveedor)->nombre }}</td>
                        <td class="text-left">{{ optional($orden->solicitante)->name }}</td>
                        <td class="text-right">{{ number_format($orden->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td>TOTAL</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right">{{ number_format($total, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </main>
</body>

</html>
