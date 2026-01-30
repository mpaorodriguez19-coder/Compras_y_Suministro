<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe de Órdenes de Compra</title>
    <style>
        /* ========= OPCIÓN 2: Estilo tipo PDF A4 ========= */
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            /* Fondo gris para simular escritorio */
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        .informe {
            width: 216mm;
            /* Ancho carta */
            min-height: 279mm;
            /* Alto carta */
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            /* Sombra tipo hoja */
            box-sizing: border-box;
        }


        /* ================= ESTILOS EXISTENTES ================= */
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

        /* Asegurar que el botón no aparezca al imprimir */
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
    <!-- Botón de imprimir -->
    <a href="{{ request()->fullUrlWithQuery(['pdf' => 1]) }}" class="btn-imprimir" target="_blank">📥 Descargar PDF</a>

    <!-- CONTENEDOR TIPO PDF -->
    <div class="informe">

        <!-- ================= ENCABEZADO ================= -->
        <div class="encabezado">

            <!-- LOGO IZQUIERDO -->
            <div>
                <img src="imagenes/logo_izq.png" class="logo">
            </div>

            <!-- CENTRO -->
            <div class="centro">
                <h1>MUNICIPALIDAD DE DANLÍ, EL PARAÍSO</h1>
                <h2>INFORME DETALLADO DE ÓRDENES DE COMPRA</h2>
                <div class="periodo">
                    PERÍODO DEL: <span id="periodoDesde">{{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}</span>
                    AL <span id="periodoHasta">{{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}</span>
                </div>
            </div>

            <!-- DERECHA: FECHA Y PAGINA -->
            <div class="info-der">
                <img src="imagenes/logo_der.jpeg" class="logo"><br><br>
                Fecha: <span id="fechaActual"></span><br>
                Página: 1
            </div>

        </div>

        <!-- ================= LINEA DIVISORA ================= -->
        <div class="linea"></div>

        <!-- =================  COMIENZA LA TABLA ================= -->
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>No. Orden</th>
                    <th>Proveedor</th>
                    <th>Descripción (Ítem)</th>
                    <th>Cant.</th>
                    <th>Precio U.</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordenes as $orden)
                    @foreach ($orden->items as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($orden->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $orden->numero }}</td>
                            <td style="text-align:left;">{{ optional($orden->proveedor)->nombre }}</td>
                            <td style="text-align:left;">{{ $item->descripcion }}</td>
                            <td>{{ number_format($item->cantidad, 2) }}</td>
                            <td>L. {{ number_format($item->precio_unitario, 2) }}</td>
                            <td>L. {{ number_format($item->valor, 2) }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="7">No hay órdenes registradas en este período.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div> <!-- FIN CONTENEDOR INFORME -->

    <script>
        // Fecha automática
        const hoy = new Date();
        document.getElementById("fechaActual").textContent =
            hoy.toLocaleDateString("es-HN");

        // Función de imprimir
        function imprimirInforme() {
            const btn = document.querySelector('.btn-imprimir');
            btn.style.display = 'none'; // Ocultar botón
            window.print();
            btn.style.display = 'block'; // Volver a mostrar
        }
    </script>

</body>

</html>
