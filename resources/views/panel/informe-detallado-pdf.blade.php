<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe Detallado</title>
    <style>
        @page {
            margin: 150px 20px 60px 20px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        /* Header Fixed */
        header {
            position: fixed;
            top: -130px;
            left: 0;
            right: 0;
            height: 120px;
        }

        /* Footer Fixed */
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

        /* Tabla Principal */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 4px;
            text-align: center;
        }

        th {
            background-color: #eaeaea;
            text-transform: uppercase;
            font-size: 10px;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        /* Simulación de Flexbox con Tabla para el Header (DomPDF no soporta flex bien) */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 5px;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
            padding: 0;
        }

        .logo {
            width: 90px;
        }

        .centro {
            text-align: center;
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
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <!-- Paginación Script -->
    <script type="text/php">
        if (isset($pdf)) {
            $x = 720; 
            $y = 100; /* Posición arriba derecha, en el header */
            $text = "{PAGE_NUM}";
            $font = null;
            $size = 11;
            $color = array(0,0,0);
            $pdf->page_text($x, $y, $text, $font, $size, $color, 0.0, 0.0, 0.0);
        }
    </script>

    <header>
        <table class="header-table">
            <tr>
                <td width="15%" style="text-align: left;">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('imagenes/logo_izq.png'))) }}"
                        class="logo">
                </td>
                <td width="70%" class="centro">
                    <h1>MUNICIPALIDAD DE DANLÍ, EL PARAÍSO</h1>
                    <h2>INFORME DETALLADO DE ÓRDENES DE COMPRA</h2>
                    <div class="periodo">
                        PERÍODO DEL: {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}
                        AL {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
                    </div>
                </td>
                <td width="15%" class="info-der">
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
        <div style="float:left">
            Generado por: {{ auth()->user()->name ?? 'Sistema' }} - {{ date('d/m/Y H:i') }}
        </div>
    </footer>

    <main>
        <table>
            <thead>
                <tr>
                    <th width="10%">Fecha</th>
                    <th width="8%">No. Orden</th>
                    <th width="20%">Proveedor</th>
                    <th width="37%">Descripción (Ítem)</th>
                    <th width="8%">Cant.</th>
                    <th width="9%">Precio U.</th>
                    <th width="8%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordenes as $orden)
                    @foreach ($orden->items as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($orden->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $orden->numero }}</td>
                            <td class="text-left">{{ optional($orden->proveedor)->nombre }}</td>
                            <td class="text-left">{{ $item->descripcion }}</td>
                            <td>{{ number_format($item->cantidad, 2) }}</td>
                            <td class="text-right">L. {{ number_format($item->precio_unitario, 2) }}</td>
                            <td class="text-right">L. {{ number_format($item->valor, 2) }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="7">No hay datos en este rango.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>
</body>

</html>
