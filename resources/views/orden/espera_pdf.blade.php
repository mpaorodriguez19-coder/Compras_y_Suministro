<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Orden de Compra {{ $orden->numero }}</title>
    <style>
        @page {
            margin: 160px 40px 150px 40px;
            /* Margenes para header y footer */
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
             margin-top: 20px;
            color: #000;
        }

        header {
            position: fixed;
            top: -160px;
            left: 0;
            right: 0;
            height: 160px;
            text-align: center;
        }
.contenido {
    margin-top: 10px;
}
        footer {
            position: fixed;
            bottom: -130px;
            left: 0;
            right: 0;
            height: 120px;
        }

        /* TABLA DE ITEMS */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            border: 1px solid black;
            background: #e0e0e0;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        td {
            border: 1px solid black;
            padding: 5px;
            vertical-align: middle;
            font-size: 11px;
        }

        /* Clases de ancho */
        .col-no {
            width: 30px;
            text-align: center;
        }

        .col-desc {
            text-align: left;
        }

        .col-unidad {
            width: 60px;
            text-align: center;
        }

        .col-cant {
            width: 60px;
            text-align: center;
        }

        .col-precio {
            width: 80px;
            text-align: right;
        }

        .col-valor {
            width: 80px;
            text-align: right;
        }

        /* HEADER ELEMENTS */
        .logo-izq {
            position: absolute;
            left: 0;
            top: 0;
            width: 65px;
        }

        .logo-der {
            position: absolute;
            right: 0;
            top: 0;
            width: 65px;
        }

        .titulo-muni {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            font-family: Arial, sans-serif;
            margin-bottom: 2px;
        }

        .subtitulo-muni {
            font-size: 12px;
            font-style: italic;
            margin: 0;
        }

        .telefonos {
            font-size: 10px;
            margin-top: 2px;
        }

        .box-orden {
            margin-top: 10px;
            font-weight: bold;
            font-size: 13px;
        }

        .numero-rojo {
            color: #d00;
            font-size: 14px;
            margin-left: 5px;
        }

        /* INFO SUPERIOR */
        .datos-superiores {
            margin-top: 10px;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 11px;
            font-weight: bold;
        }

        .dato-row {
            margin-bottom: 5px;
        }

        .label {
            display: inline-block;
            width: 80px;
        }

        /* TOTALES */
        .bloque-totales {
            width: 100%;
            border: 1px solid black;
            border-top: none;
            margin-bottom: 10px;
            page-break-inside: avoid;
            /* Evitar que se parta a mitad */
        }

        .fila-total {
            display: table;
            /* Simular tabla para alinacion */
            width: 100%;
        }

        .celda-izq {
            display: table-cell;
            width: 70%;
            padding: 5px;
            border-right: 1px solid black;
            font-size: 10px;
            vertical-align: top;
        }

        .celda-der {
            display: table-cell;
            width: 30%;
            vertical-align: top;
            padding: 0;
        }

        .total-item {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid black;
            padding: 3px 5px;
            font-size: 11px;
        }

        .total-item:last-child {
            border-bottom: none;
        }

        .total-final {
            background: #f0f0f0;
            font-weight: bold;
        }

        /* FOOTER / FIRMAS */
        .firmas-container {
            width: 100%;
            margin-top: 50px;
        }

        .firma-box {
            display: inline-block;
            width: 30%;
            text-align: center;
            vertical-align: top;
            margin-top: 20px;
        }

        .linea-firma {
            border-top: 1px solid black;
            width: 80%;
            margin: 0 auto 5px auto;
            display: block;
        }

        .legal-text {
            font-size: 9px;
            text-align: justify;
            margin-bottom: 10px;
            line-height: 1.1;
        }
    </style>
</head>

<body>

    <!-- SCRIPT PHP para paginación (DomPDF) -->
    <script type="text/php">
        if (isset($pdf)) {
            $x = 520;
            $y = 750; /* Posicion abajo derecha aprox */
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $font = null;
            $size = 9;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
    </script>

    <header>
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('imagenes/logo_izq.png'))) }}"
            class="logo-izq">
        <h1 class="titulo-muni">MUNICIPALIDAD DE DANLI, EL PARAISO</h1>
        <p class="subtitulo-muni">Departamento de El Paraiso, Honduras, C.A.</p>
        <p class="telefonos">Teléfono: 2763-2080/2763-2405 Telefax: 2763-2638</p>

        <div class="box-orden">
            ORDEN DE COMPRA No. <span class="numero-rojo">{{ $orden->numero }}</span>
        </div>
        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('imagenes/logo_der.jpeg'))) }}"
            class="logo-der">

        <hr style="margin-top: 5px; margin-bottom: 5px;">

        <div class="datos-superiores">
            <div class="dato-row">
                <span class="label">LUGAR:</span>
                <span>{{ $orden->lugar ?: 'DANLI, EL PARAISO' }}</span>
            </div>
            <div class="dato-row">
                <span class="label">FECHA:</span>
                <span>{{ \Carbon\Carbon::parse($orden->fecha)->isoFormat('D [de] MMMM, YYYY') }}</span>
            </div>
            <div class="dato-row">
                <span class="label">A:</span>
                <span>
                    {{ $orden->proveedor->nombre ?? '—' }}
                    @if ($orden->proveedor && ($orden->proveedor->rtn || $orden->proveedor->nit))
                        / {{ $orden->proveedor->rtn ?? $orden->proveedor->nit }}
                    @endif
                </span>
            </div>
        </div>

        <div style="font-size: 11px; margin-top: 5px; font-style: italic; text-align: left;">
            Estimados señores: Agradecemos entregar los materiales o prestar los servicios indicados en el siguiente
            cuadro.
        </div>
    </header>

    <footer>
        <div class="legal-text">
            <strong>SUMINISTRANTE:</strong> Para cancelar su cuenta envíe esta orden con Factura en triplicado firmado
            con la siguiente certificación.
            Certifico(amos) que esta cuenta es justa y correcta y que no ha sido pagada. La falta de cualquiera de estos
            requisitos atrasará la cancelacion de la cuenta.
        </div>

        <div class="firmas-container">
            <div class="firma-box">
                <span class="linea-firma"></span>
                <strong>Jefe de Compras</strong>
            </div>
            <div class="firma-box">
                <span class="linea-firma"></span>
                <strong>Gerente Administrativo</strong>
            </div>
            <div class="firma-box">
                <span class="linea-firma"></span>
                <strong>Alcalde Municipal</strong>
            </div>
        </div>

        <div style="font-size: 9px; margin-top: 5px; text-align: left;">
            Hecho Por: {{ auth()->user()->name ?? 'SISTEMA' }}
        </div>
    </footer>

    <!-- CONTENIDO (TABLA) -->
    <main>
        <table>
            <thead>
                <tr>
                    <th class="col-no">No.</th>
                    <th class="col-desc">DESCRIPCION</th>
                    <th class="col-unidad">UNIDAD</th>
                    <th class="col-cant">CANT.</th>
                    <th class="col-precio">PRECIO U.</th>
                    <th class="col-valor">VALOR L.</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orden->items as $i => $item)
                    <tr>
                        <td class="col-no">{{ $i + 1 }}</td>
                        <td class="col-desc">{{ $item->descripcion }}</td>
                        <td class="col-unidad">{{ $item->unidad }}</td>
                        <td class="col-cant">{{ number_format($item->cantidad, 2) }}</td>
                        <td class="col-precio">{{ number_format($item->precio_unitario, 2) }}</td>
                        <td class="col-valor">{{ number_format($item->valor, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- TOTALES (Al final de la tabla) -->
        <div class="bloque-totales">
            <div class="fila-total">
                <div class="celda-izq">
                    <strong>CONCEPTO / USO:</strong><br>
                    {{ $orden->concepto ? strtoupper($orden->concepto) : '---' }}
                    <br><br>
                    <strong>Solicitado por:</strong> {{ optional($orden->solicitante)->name }}
                </div>
                <div class="celda-der">
                    <div class="total-item">
                        <span>Sub - Total L.</span>
                        <span>{{ number_format($orden->subtotal, 2) }}</span>
                    </div>
                    <div class="total-item">
                        <span>Descuento</span>
                        <span>{{ number_format($orden->descuento, 2) }}</span>
                    </div>
                    <div class="total-item">
                        <span>Impuesto</span>
                        <span>{{ number_format($orden->impuesto, 2) }}</span>
                    </div>
                    <div class="total-item total-final">
                        <span>Total Pago</span>
                        <span>{{ number_format($orden->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>

</html>
