<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe de Transparencia</title>

    <style>
        /* ================== CONFIGURACIÓN PDF ================== */
        @page {
            margin: 110px 20px 60px 20px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
        }

        /* ================== HEADER ================== */
        header {
            position: fixed;
            top: -95px;
            left: 0;
            right: 0;
            height: 90px;
            border-bottom: 1px solid #000;
        }

        .header-container {
            width: 100%;
            height: 100%;
            position: relative;
            text-align: center;
        }

        .logo-left,
        .logo-right {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
        }

        .logo-left {
            left: 0;
        }

        .logo-right {
            right: 0;
        }

        .header-title h1 {
            font-size: 15px;
            margin: 0;
        }

        .header-title h2 {
            font-size: 12px;
            margin: 2px 0;
        }

        .header-title div {
            font-size: 10px;
        }

        /* ================== FOOTER ================== */
        footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            height: 40px;
            font-size: 9px;
            border-top: 1px solid #ccc;
        }

        .footer-left {
            float: left;
        }

        .footer-center {
            text-align: center;
        }

        .footer-right {
            float: right;
        }

        /* ================== TABLA ================== */
        table.content-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.content-table th,
        table.content-table td {
            border: 1px solid #000;
            padding: 3px;
            font-size: 9px;
            vertical-align: top;
            word-wrap: break-word;
        }

        table.content-table th {
            background-color: #eee;
            text-transform: uppercase;
            text-align: center;
        }

        thead {
            display: table-header-group;
        }

        tbody tr {
            page-break-inside: avoid;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>

    <!-- ================== HEADER ================== -->
    <header>
        <div class="header-container">

            <div class="logo-left">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('imagenes/logo_izq.png'))) }}"
                    style="width:65px;">
            </div>

            <div class="header-title">
                <h1>MUNICIPALIDAD DE DANLÍ</h1>
                <h2>INFORME DE TRANSPARENCIA</h2>
                <div>
                    PERÍODO:
                    {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}
                    AL
                    {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
                </div>
            </div>

            <div class="logo-right">
                <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('imagenes/logo_der.jpeg'))) }}"
                    style="width:65px;">
            </div>

        </div>
    </header>

    <!-- ================== FOOTER ================== -->
    <footer>
        <div class="footer-left">
            Generado por: {{ auth()->user()->name ?? 'Sistema' }}
        </div>

        <div class="footer-center">
            Fecha: {{ date('d/m/Y') }}
        </div>

        <div class="footer-right">
            Pág {PAGE_NUM} - {PAGE_COUNT}
        </div>
    </footer>

    <!-- ================== CONTENIDO ================== -->
    <main>
        <table class="content-table">
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
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($orden->fecha)->format('d/m/Y') }}
                        </td>

                        <td class="text-center">
                            {{ $orden->numero }}
                        </td>

                        <td class="text-left">
                            {{ optional($orden->proveedor)->nombre }}
                        </td>

                        <td class="text-center">
                            {{ optional($orden->proveedor)->rtn ?? optional($orden->proveedor)->nit }}
                        </td>

                        <td class="text-left">
                            {{ \Illuminate\Support\Str::limit($orden->concepto ?: optional($orden->items->first())->descripcion, 120) }}
                        </td>

                        <td class="text-right">
                            L. {{ number_format($orden->total, 2) }}
                        </td>

                        <td class="text-center">
                            Completado
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>

</body>

</html>
