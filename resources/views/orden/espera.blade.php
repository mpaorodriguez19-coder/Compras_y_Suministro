<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Orden de Compra {{ $orden->numero }}</title>

    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            padding: 20px;
            color: #000;
        }

        .hoja {
            width: 216mm;
            /* Carta */
            min-height: 279mm;
            background: white;
            padding: 20px 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, .3);
            box-sizing: border-box;
            font-size: 12px;
            position: relative;
        }

        /* ENCABEZADO */
        .encabezado {
            text-align: center;
            position: relative;
            margin-bottom: 5px;
        }

        .logo-izq {
            position: absolute;
            left: 0;
            top: 0;
            width: 70px;
        }

        .logo-der {
            position: absolute;
            right: 0;
            top: 0;
            width: 70px;
        }

        .titulo-muni {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .subtitulo-muni {
            font-size: 14px;
            font-style: italic;
            margin: 2px 0;
        }

        .telefonos {
            font-size: 12px;
            margin-bottom: 10px;
        }

        .box-orden {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 5px;
            font-family: Arial, sans-serif;
            font-weight: bold;
            font-size: 14px;
        }

        .box-numero {
            border: 1px solid black;
            border-radius: 10px;
            padding: 5px 20px;
            margin-left: 10px;
            color: #d00;
            /* Color rojizo típico de folios */
            font-size: 16px;
        }

        hr {
            border: 0;
            border-top: 1px solid #000;
            margin: 5px 0 15px 0;
        }

        /* DATOS SUPERIORES */
        .datos-superiores {
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-weight: bold;
            line-height: 1.8;
        }

        .dato-row {
            display: flex;
        }

        .dato-label {
            width: 80px;
            text-transform: uppercase;
        }

        .dato-val {
            flex: 1;
            text-transform: uppercase;
        }

        .saludo {
            margin: 15px 0 5px 0;
            font-family: Arial, sans-serif;
        }

        /* TABLA */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        th {
            border: 1px solid black;
            background: #e0e0e0;
            padding: 4px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        td {
            border: 1px solid black;
            padding: 4px;
            vertical-align: middle;
        }

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

        .empty-row td {
            height: 18px;
        }

        /* Filas vacías de relleno */

        /* SECCION TOTALES + SOLICITADO */
        .bloque-totales {
            display: flex;
            border: 1px solid black;
            border-top: none;
            /* Ya la tabla tiene borde */
            font-family: Arial, sans-serif;
        }

        .bloque-info {
            flex: 1;
            padding: 5px;
            font-size: 10px;
            border-right: 1px solid black;
        }

        .bloque-cifras {
            width: 300px;
        }

        .row-total {
            display: flex;
            border-bottom: 1px solid black;
        }

        .row-total:last-child {
            border-bottom: none;
        }

        .label-total {
            flex: 1;
            padding: 4px;
            padding-left: 10px;
            border-right: 1px solid black;
        }

        .monto-total {
            width: 100px;
            padding: 4px;
            text-align: right;
        }

        /* PIE DE PAGINA / FIRMAS */
        .footer-section {
            margin-top: 5px;
            display: flex;
            font-family: "Times New Roman", serif;
            font-size: 12px;
        }

        .footer-left {
            width: 45%;
            padding-top: 10px;
        }

        .footer-right {
            width: 55%;
            padding-top: 40px;
            /* Espacio para firma jefe compras */
            text-align: center;
        }

        .texto-legal {
            font-size: 11px;
            text-align: justify;
            margin-top: 5px;
            line-height: 1.2;
        }

        .firma-box {
            margin-bottom: 40px;
        }

        .linea {
            display: inline-block;
            width: 80%;
            border-top: 1px solid black;
            margin-bottom: 5px;
        }

        .firmas-bottom {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            text-align: center;
            font-weight: bold;
        }

        .firma-col {
            width: 45%;
        }

        .copia-hecho {
            margin-top: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .btn-imprimir {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        @media print {
            .btn-imprimir {
                display: none;
            }

            body {
                background: white;
                padding: 0;
            }

            .hoja {
                box-shadow: none;
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>

<body>

    <div style="position: fixed; top: 20px; right: 20px; display:flex; gap:10px;">
        <a href="{{ route('orden.pdf', ['id' => $orden->id, 'tipo' => 'copia']) }}" class="btn-imprimir"
            style="background: #6c757d;" target="_blank">📄 Imprimir Copia</a>
        <a href="{{ route('orden.pdf', ['id' => $orden->id, 'tipo' => 'original']) }}" class="btn-imprimir"
            target="_blank">🖨 Imprimir Original</a>
    </div>

    <div class="hoja">

        <!-- ENCABEZADO -->
        <div class="encabezado">
            <!-- Ajusta rutas de logos si existen, usando asset() -->
            <img src="{{ asset('imagenes/logo_izq.png') }}" class="logo-izq" alt="Escudo">

            <h1 class="titulo-muni">MUNICIPALIDAD DE DANLI, EL PARAISO</h1>
            <p class="subtitulo-muni">Departamento de El Paraiso, Honduras, C.A.</p>
            <p class="telefonos">Teléfono: 2763-2080/2763-2405 Telefax: 2763-2638</p>

            <div class="box-orden">
                ORDEN DE COMPRA No. <div class="box-numero">{{ $orden->numero }}</div>
            </div>

            <img src="{{ asset('imagenes/logo_der.jpeg') }}" class="logo-der" alt="Logo">
        </div>

        <hr>

        <!-- DATOS -->
        <div class="datos-superiores">
            <div class="dato-row">
                <span class="dato-label">LUGAR:</span>
                <span class="dato-val">{{ $orden->lugar ?: 'DANLI, EL PARAISO' }}</span>
            </div>
            <div class="dato-row">
                <span class="dato-label">FECHA:</span>
                <span class="dato-val">
                    {{ \Carbon\Carbon::parse($orden->fecha)->locale('es')->isoFormat('D [de] MMMM, YYYY') }}

                </span>
            </div>
            <div class="dato-row">
                <span class="dato-label">A:</span>
                <span class="dato-val">
                    {{ $orden->proveedor->nombre ?? '—' }}
                    @if ($orden->proveedor && ($orden->proveedor->rtn || $orden->proveedor->nit))
                        / {{ $orden->proveedor->rtn ?? $orden->proveedor->nit }}
                    @endif
                </span>
            </div>
        </div>

        <div class="saludo">
            Estimados señores:<br>
            Agradecemos entregar los materiales o prestar los servicios indicados en el siguiente cuadro
        </div>

        <!-- TABLA DE ITEMS -->
        <table>
            <thead>
                <tr>
                    <th class="col-no">No.</th>
                    <th class="col-desc">DESCRIPCION</th>
                    <th class="col-unidad">UNIDAD</th>
                    <th class="col-cant">CANTIDAD</th>
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

                {{-- Relleno de filas vacías para simular papel --}}
                @for ($j = count($orden->items); $j < 12; $j++)
                    <tr class="empty-row">
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <!-- BLOQUE TOTALES E INFOS -->
        <div class="bloque-totales">
            <div class="bloque-info">
                <div style="margin-bottom: 5px;">
                    <strong style="font-size: 10px;">
                        {{ $orden->concepto ? strtoupper($orden->concepto) : 'UTILIZADOS POR EMPLEADOS DEL PLANTEL EN RECOLECCION DE DESECHOS SOLIDOS EN TODA LA CIUDAD,CM-AMD-CS-1083-2025,01-00-000-005-000-36400-15-013-01,EXP-43510' }}
                    </strong>
                </div>
                <div>
                    <strong>Solicitado por:</strong> {{ optional($orden->solicitante)->name }}
                </div>
            </div>

            <div class="bloque-cifras">
                <div class="row-total">
                    <div class="label-total">Sub - Total L.</div>
                    <div class="monto-total">{{ number_format($orden->subtotal, 2) }}</div>
                </div>
                <div class="row-total">
                    <div class="label-total">Descuento:</div>
                    <div class="monto-total">{{ number_format($orden->descuento, 2) }}</div>
                </div>
                <div class="row-total">
                    <div class="label-total">Impuesto:</div>
                    <div class="monto-total">{{ number_format($orden->impuesto, 2) }}</div>
                </div>
                <div class="row-total" style="font-weight: bold; background: #f9f9f9;">
                    <div class="label-total">Total Pago:</div>
                    <div class="monto-total">{{ number_format($orden->total, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- PIE DE PAGINA (FIRMAS) -->
        <div class="footer-section">
            <!-- Lado Izquierdo -->
            <div class="footer-left">
                <strong>SUMINISTRANTE:</strong>
                <p class="texto-legal">
                    Para cancelar su cuenta envíe esta orden con Factura en triplicado firmado con la siguiente
                    certificación.
                    Certifico(amos) que esta cuenta es justa y correcta y que no ha sido pagada. La falta de cualquiera
                    de estos
                    requisitos atrasará la cancelacion de la cuenta.
                </p>

                <div class="copia-hecho">
                    Copia<br><br>
                    Hecho Por.. {{ auth()->user()->name ?? 'SISTEMA' }}
                </div>
            </div>

            <!-- Lado Derecho (Firmas) -->
            <div class="footer-right">

                <div class="firma-box">
                    <span class="linea"></span><br>
                    <strong>Jefe de Compras</strong>
                </div>

                <div class="firmas-bottom">
                    <div class="firma-col">
                        <span class="linea"></span><br>
                        Gerente Administrativo
                    </div>
                    <div class="firma-col">
                        <span class="linea"></span><br>
                        Alcalde Municipal
                    </div>
                </div>

            </div>
        </div>

    </div>

</body>

</html>
