<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Resumen de Compras por Proveedor</title>
    <!-- Bootstrap for Pagination -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        .informe {
            width: 216mm;
            min-height: 279mm;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            box-sizing: border-box;
        }

        .encabezado {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 5px;
        }

        .logo {
            width: 90px;
        }

        .centro {
            text-align: center;
            flex-grow: 1;
        }

        .centro h1,
        .centro h2 {
            margin: 3px;
        }

        .info-der {
            text-align: right;
            font-size: 12px;
        }

        .periodo {
            text-align: center;
            font-weight: bold;
            margin-top: 8px;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .linea {
            border-bottom: 2px solid black;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
            font-size: 13px;
        }

        th {
            background-color: #eaeaea;
        }

        /* Ocultar elementos al imprimir */
        @media print {

            .no-print,
            .btn-imprimir-container,
            .pagination-container {
                display: none !important;
            }

            body {
                background: white;
            }

            .informe {
                box-shadow: none;
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>

<body>

    <!-- Botón de imprimir -->
    <div class="btn-imprimir-container d-flex gap-2" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <a href="{{ request()->fullUrlWithQuery(['pdf' => 1]) }}" class="btn btn-primary">🖨️ Imprimir</a>
    </div>

    <div class="informe">
        <div class="encabezado">
            <div><img src="imagenes/logo_izq.png" class="logo"></div>
            <div class="centro">
                <h1>MUNICIPALIDAD DE DANLÍ, EL PARAÍSO</h1>
                <h2>RESUMEN GLOBAL POR PROVEEDOR</h2>
                <div class="periodo">
                    PERÍODO DEL: {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}
                    AL {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
                </div>
            </div>
            <div class="info-der">
                <img src="imagenes/logo_der.jpeg" class="logo"><br><br>
                Fecha: {{ date('d/m/Y') }}<br>
                <!-- Página: 1 -->
            </div>
        </div>
        <div class="linea"></div>

        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">No.</th>
                    <th style="width: 60%; text-align:left; padding-left:10px;">Nombre del Proveedor</th>
                    <th style="width: 30%;">Total Compras (Lps.)</th>
                </tr>
            </thead>
            <tbody>
                @php $granTotal = 0; @endphp
                @forelse($proveedores as $index => $proveedor)
                    <tr>
                        <td>{{ $proveedores->firstItem() + $index }}</td>
                        <td style="text-align:left; padding-left:10px;">{{ $proveedor->nombre }}</td>
                        <td style="text-align:right; font-weight:bold;">L.
                            {{ number_format($proveedor->ordenes_sum_total, 2) }}</td>
                    </tr>
                    @php $granTotal += $proveedor->ordenes_sum_total; @endphp
                @empty
                    <tr>
                        <td colspan="3">No hay compras en este período.</td>
                    </tr>
                @endforelse

                <!-- GRAN TOTAL -->
                <tr style="background-color: #f0f0f0; font-size: 14px;">
                    <td colspan="2" style="text-align: right; font-weight: bold;">TOTAL PÁGINA:</td>
                    <td style="text-align: right; font-weight: bold;">L. {{ number_format($granTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- PAGINACIÓN Y TOTAL DE PÁGINAS -->
        <div class="d-flex justify-content-end align-items-center mt-4 gap-3">
            <div class="pagination-container d-print-none">
                {{ $proveedores->links('pagination::bootstrap-5') }}
            </div>
            <div style="font-size: 14px; font-weight: bold;">
                Pág {{ $proveedores->currentPage() }} - {{ $proveedores->lastPage() }}
            </div>
        </div>

    </div>

</body>

</html>
