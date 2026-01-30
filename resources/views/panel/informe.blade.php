<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Listado de Orden de Compra</title>
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
            display: flex;
            flex-direction: column;
            justify-content: space-between;
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

        /* TABLA ARRIBA */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: none;
        }

        th,
        td {
            padding: 6px;
            font-size: 13px;
            border: none;
        }

        td.numero {
            width: 5%;
            text-align: left;
        }

        td.fecha {
            width: 10%;
            text-align: left;
        }

        td.numeropedido {
            width: 10%;
            text-align: left;
        }

        td.nombre {
            width: 35%;
            text-align: left;
            padding-left: 8px;
        }

        td.solicitada {
            width: 25%;
            text-align: left;
            padding-left: 8px;
        }

        td.valor {
            width: 15%;
            text-align: right;
            padding-right: 10px;
        }

        /* TOTAL al final con doble línea */
        .total-row td {
            border-top: 2px solid black;
            font-weight: bold;
        }

        .total-row td.valor {
            border-top: 2px solid black;
        }

        /* Botón de imprimir */
        .btn-imprimir {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }

        .btn-imprimir:hover {
            background-color: #0056b3;
        }

        @media print {
            .btn-imprimir {
                display: none;
            }

            body {
                background: white;
            }

            .informe {
                box-shadow: none;
                width: auto;
                min-height: auto;
            }
        }
    </style>
</head>

<body>

    <!-- Botón de imprimir -->
    <a href="{{ request()->fullUrlWithQuery(['pdf' => 1]) }}" class="btn-imprimir" target="_blank">📥 Descargar PDF</a>

    <div class="informe">

        <!-- ENCABEZADO -->
        <div class="encabezado">
            <div><img src="imagenes/logo_izq.png" class="logo"></div>

            <div class="centro">
                <h1>MUNICIPALIDAD DE DANLÍ, EL PARAÍSO</h1>
                <h2>LISTADO DE ORDEN DE COMPRA</h2>
                <div class="periodo">
                    PERÍODO DEL: {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}
                    AL {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
                </div>
            </div>

            <div class="info-der">
                <img src="imagenes/logo_der.jpeg" class="logo"><br><br>
                Fecha: <span id="fechaActual"></span><br>
                Página: 1
            </div>
        </div>

        <div class="linea"></div>

        <!-- TABLA ARRIBA CON NUMERO, FECHA, NUMERO PEDIDO, NOMBRE Y VALOR -->
        <table id="tablaArriba">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Fecha</th>
                    <th>Numero F.</th>
                    <th>Proveedor</th>
                    <th>Solicitada</th>
                    <th>Valor L.</th>
                </tr>
            </thead>
            <tbody>
                @php $contador = 1; @endphp
                @forelse($ordenes as $orden)
                    <tr>
                        <td class="numero">{{ $contador++ }}</td>
                        <td class="fecha">{{ \Carbon\Carbon::parse($orden->fecha)->format('d/m/Y') }}</td>
                        <td class="numeropedido">{{ $orden->numero }}</td>
                        <td class="nombre">{{ optional($orden->proveedor)->nombre }}</td>
                        <td class="solicitada">{{ optional($orden->solicitante)->name }}</td>
                        <td class="valor">{{ number_format($orden->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;">No hay órdenes en este rango de fechas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- ESPACIO EN BLANCO -->
        <div style="flex-grow:1;"></div>

        <!-- TOTAL ABAJO -->
        <table>
            <tfoot>
                <tr class="total-row">
                    <td>TOTAL</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="valor" id="totalValor">0.00</td>
                </tr>
            </tfoot>
        </table>

    </div>

    <script>
        const hoy = new Date();
        document.getElementById("fechaActual").textContent =
            hoy.toLocaleDateString("es-HN");

        function imprimirInforme() {
            const btn = document.querySelector('.btn-imprimir');
            btn.style.display = 'none';
            window.print();
            btn.style.display = 'block';
        }

        // Calcular total de la tabla arriba
        function calcularTotal() {
            const celdas = document.querySelectorAll('#tablaArriba tbody td.valor');
            let suma = 0;
            celdas.forEach(celda => {
                const valor = parseFloat(celda.textContent.replace(',', '')) || 0;
                suma += valor;
            });
            document.getElementById('totalValor').textContent = suma.toFixed(2);
        }

        calcularTotal();
    </script>

</body>

</html>
