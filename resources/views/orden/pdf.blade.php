<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Compra</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            margin: 40px;
        }

        .titulo-centro {
            text-align: center;
            font-weight: bold;
            font-size: 15px;
        }

        .tabla-header {
            width: 100%;
            margin-top: 10px;
        }

        .tabla-header td {
            vertical-align: top;
            padding: 4px;
        }

        .tabla-detalle {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .tabla-detalle th,
        .tabla-detalle td {
            border: 1px solid black;
            padding: 5px;
            font-size: 12px;
            text-align: center;
        }

        .tabla-detalle td.desc {
            text-align: left;
        }

        .subtotales {
            width: 40%;
            float: right;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .subtotales td {
            border: 1px solid black;
            padding: 5px;
            font-size: 12px;
        }
    </style>
</head>
<body>

<table width="100%" style="margin-bottom:10px;">
    <tr>
        <td width="20%" align="left">
            <img src="{{ public_path('imagenes/logo_izq.png') }}" width="80">
        </td>

        <td width="60%" align="center" class="titulo-centro">
            MUNICIPALIDAD DE DANLÍ, EL PARAÍSO<br>
            Departamento de El Paraíso, Honduras, C.A.<br>
            Tel: 2763-2080 / 2763-2405<br><br>
            <strong>ORDEN DE COMPRA No. {{ $orden->numero }}</strong>
        </td>

        <td width="20%" align="right">
            <img src="{{ public_path('imagenes/logo_der.png') }}" width="80">
        </td>
    </tr>
</table>

<p>
    Estimados señores:<br>
    Agradecemos entregar los materiales o prestar los servicios indicados
    en el siguiente cuadro:
</p>

<table class="tabla-detalle">
    <thead>
        <tr>
            <th>No.</th>
            <th>DESCRIPCIÓN</th>
            <th>UNIDAD</th>
            <th>CANTIDAD</th>
            <th>PRECIO L.</th>
            <th>VALOR L.</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orden->items as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td class="desc">{{ $item->descripcion }}</td>
            <td>{{ $item->unidad }}</td>
            <td>{{ $item->cantidad }}</td>
            <td>{{ number_format($item->precio_unitario, 2) }}</td>
            <td>{{ number_format($item->valor, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="subtotales">
    <tr>
        <td><strong>Sub Total</strong></td>
        <td>L. {{ number_format($orden->subtotal, 2) }}</td>
    </tr>
    <tr>
        <td><strong>Descuento</strong></td>
        <td>L. {{ number_format($orden->descuento, 2) }}</td>
    </tr>
    <tr>
        <td><strong>Impuesto</strong></td>
        <td>L. {{ number_format($orden->impuesto, 2) }}</td>
    </tr>
    <tr>
        <td><strong>Total</strong></td>
        <td><strong>L. {{ number_format($orden->total, 2) }}</strong></td>
    </tr>
</table>

<div style="clear: both;"></div>

<br><br>

<p><strong>SOLICITADO POR:</strong> {{ $orden->solicitante->name ?? '—' }}</p>

<br><br>

<table width="100%">
    <tr>
        <td style="text-align:center">
            _____________________________<br>
            Elaborado por
        </td>
        <td style="text-align:center">
            _____________________________<br>
            Autorizado
        </td>
    </tr>
</table>

</body>
</html>
