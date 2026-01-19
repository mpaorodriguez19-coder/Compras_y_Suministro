<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Compra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        :root {
            --accent-1: #0ea5a4;
            --accent-2: #06b6d4;
            --card-bg: linear-gradient(180deg, #ffffff 0%, #f6fffd 100%);
            --right-panel: linear-gradient(180deg, #eefaf6 0%, #ddf6f4 100%);
        }

        body {
            background: linear-gradient(180deg, #e8faf8 0%, #dff7f6 100%);
            font-family: "Helvetica Neue", Arial, sans-serif;
            padding: 18px;
        }

        .main-card {
            background: var(--card-bg);
            border-radius: 14px;
            box-shadow: 0 8px 28px rgba(6, 22, 22, 0.08);
            padding: 18px;
        }

        .header-bar {
            background: linear-gradient(90deg, var(--accent-2), var(--accent-1));
            color: white;
            padding: 10px 14px;
            border-radius: 10px;
            margin-bottom: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .right-panel {
            background: var(--right-panel);
            padding: 10px;
            border-radius: 10px;
            width: 100%;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
        }

        .btn-as-panel {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            font-weight: 500;
            border-radius: 8px;
            text-decoration: none;
            color: #000;
            background: #fff;
            box-shadow: 0 3px 8px rgba(11, 22, 22, 0.05);
            margin-bottom: 6px;
            transition: transform 0.1s, box-shadow 0.2s;
        }

        .btn-as-panel:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(11, 22, 22, 0.1);
        }

        .btn-as-panel .icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            color: white;
            font-size: 16px;
        }

        .table-responsive {
            max-height: 1000px;
            overflow-y: auto;
        }

        .table thead th {
            background: rgba(255, 255, 255, 0.5);
            border-top: none;
            border-bottom: 2px solid rgba(6, 22, 22, 0.05);
        }

        td .form-control,
        th .form-control {
            height: 28px;
            padding: .25rem .5rem;
        }

        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        .no-arrows {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: textfield;
        }

        .small-checkbox {
            width: 16px;
            height: 16px;
            margin-right: 6px;
        }

        .valor-read {
            text-align: right;
            font-weight: 600;
            border: none;
            background: transparent;
            color: #0f766e;
            width: 100%;
        }

        .totals-panel {
            background: rgba(255, 255, 255, 0.7);
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 6px 12px rgba(6, 22, 22, 0.04);
        }

        .form-group {
            margin-bottom: 6px;
        }

        input,
        select,
        textarea {
            font-size: 14px;
        }

        .no-margin {
            margin: 0 !important;
        }

        .no-padding {
            padding: 0 !important;
        }

        @media (max-width: 1100px) {
            .right-panel {
                width: 100%;
                margin-top: 12px;
                position: static !important;
            }
        }

        /* Autocomplete Styles */
        .autocomplete-wrapper {
            position: relative;
            width: 100%;
        }

        .autocomplete-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 999;
            background: white;
            border: 1px solid #ddd;
            border-radius: 0 0 6px 6px;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .autocomplete-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .autocomplete-item:hover,
        .autocomplete-item.active {
            background-color: #f0f9ff;
            color: #0ea5a4;
        }

        .autocomplete-item:last-child {
            border-bottom: none;
        }
    </style>
</head>

<body>
    <div class="container main-card p-0">

        <!-- Barra superior -->
        <div class="header-bar">
            <h4 class="m-0">Orden de Compra</h4>
        </div>

        <div class="row g-2 mb-0">
            <!-- Formulario principal -->
            <div class="col-lg-10 pe-0">
                {{-- ALERTA DE ERRORES --}}
                @if (session('error'))
                    <div class="alert alert-danger shadow-sm">
                        {{ session('error') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('orden.reponer.guardar') }}" method="POST" id="ordenForm">
                    @csrf
                    <div class="p-2 rounded shadow-sm bg-light mb-0">

                        <!-- Fecha, Proveedor, Lugar, Solicitado por -->
                        <div class="d-flex flex-wrap align-items-center mb-1 gap-2">
                            <label for="fecha" class="form-label fw-bold mb-0" style="width:80px;">Fecha:</label>
                            <input id="fecha" name="fecha" type="date"
                                class="form-control form-control-sm shadow-sm" style="max-width:150px;"
                                value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="d-flex flex-wrap align-items-center mb-1">
                            <label for="proveedor" class="form-label fw-bold me-2 mb-0"
                                style="width:120px;">Proveedor:</label>
                            <div class="input-group input-group-sm autocomplete-wrapper" style="max-width:400px;">
                                <input id="proveedor" name="proveedor" type="text" class="form-control shadow-sm"
                                    placeholder="Proveedor..." autocomplete="off">
                                <button type="button" class="btn btn-outline-primary btn-sm">🔍</button>
                                <div id="listaProveedores" class="autocomplete-list"></div>
                            </div>
                            {{-- <button type="button" class="btn btn-outline-success btn-sm ms-1"
                                onclick="abrirModalProveedor()" title="Editar Datos del Proveedor">➕</button> --}}

                            <!-- Inputs Ocultos para Datos Extendidos -->
                            <input type="hidden" name="proveedor_rtn" id="h_proveedor_rtn">
                            <input type="hidden" name="proveedor_telefono" id="h_proveedor_telefono">
                            <input type="hidden" name="proveedor_correo" id="h_proveedor_correo">
                            <input type="hidden" name="proveedor_direccion" id="h_proveedor_direccion">
                        </div>
                        <div class="d-flex flex-wrap align-items-center mb-1">
                            <label for="lugar" class="form-label fw-bold me-2 mb-0"
                                style="width:120px;">Lugar:</label>
                            <input id="lugar" name="lugar" type="text"
                                class="form-control form-control-sm shadow-sm" placeholder="Sede / ubicación"
                                style="max-width:400px;">
                        </div>
                        <div class="d-flex flex-wrap align-items-center mb-1">
                            <label for="solicitado" class="form-label fw-bold me-2 mb-0" style="width:120px;">Solicitado
                                por:</label>
                            <div class="input-group input-group-sm autocomplete-wrapper" style="max-width:400px;">
                                <input id="solicitado" name="solicitado" type="text" class="form-control shadow-sm"
                                    placeholder="Usuario solicitante..." autocomplete="off">
                                <button type="button" class="btn btn-outline-primary btn-sm">🔍</button>
                                <div id="listaUsuarios" class="autocomplete-list"></div>
                            </div>
                        </div>



                        <!-- Tabla de items -->
                        <div class="table-responsive mt-2">
                            <table id="itemsTable" class="table table-bordered align-middle mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width:50px">Cant.</th>
                                        <th>Descripción</th>
                                        <th style="width:50px">Unidad</th>
                                        <th style="width:50px">Precio Unitario</th>
                                        <th style="width:50px">Descuento</th>
                                        <th style="width:50px">Valor L.</th>
                                        <th style="width:50px">Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <!-- Concepto + Totales -->
                        <div class="d-flex justify-content-between align-items-start gap-2 mt-2">
                            <div class="flex-grow-1">
                                <label class="form-label">Concepto</label>
                                <textarea name="concepto" rows="3" class="form-control"></textarea>
                                <div class="mt-1">
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="agregarFila()">+ Agregar fila</button>
                                </div>
                            </div>
                            <div style="width:260px;">
                                <div class="totals-panel">
                                    <input type="hidden" name="sub_total" id="subTotalInput" value="0.00">
                                    <input type="hidden" name="descuento_total" id="descTotalInput" value="0.00">
                                    <input type="hidden" name="impuesto" id="impuestoInput" value="0.00">
                                    <input type="hidden" name="total" id="totalInput" value="0.00">

                                    <div class="d-flex justify-content-between">
                                        <div>Sub-Total</div>
                                        <div><strong id="subTotal">0.00</strong></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <div>Descuento Total</div>
                                        <div id="descTotal">0.00</div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <div>Impuesto</div>
                                        <div id="impuesto">0.00</div>
                                    </div>
                                    <hr />
                                    <div class="d-flex justify-content-between">
                                        <div class="fw-bold">Total</div>
                                        <div class="fw-bold" id="total">0.00</div>
                                    </div>
                                    <div class="mt-2 text-center">
                                        <button type="submit" class="btn btn-sm btn-outline-success">💾
                                            Guardar</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="salir()">Salir</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <!-- Panel derecho -->
            <div class="col-lg-2 ps-0">
                <div class="right-panel position-sticky" style="top:0;">

                    <!-- Rango de Fechas -->
                    <div class="mb-3 p-2 bg-white rounded shadow-sm border">
                        <small class="fw-bold d-block mb-1">📅 Rango de Fechas</small>
                        <div class="mb-1">
                            <label for="desde" class="form-label mb-0" style="font-size:11px;">Desde:</label>
                            <input type="date" id="desde" onchange="handleInput()"
                                class="form-control form-control-sm"
                                value="{{ request('desde', Carbon\Carbon::now()->subMonth()->toDateString()) }}">
                        </div>
                        <div>
                            <label for="hasta" class="form-label mb-0" style="font-size:11px;">Hasta:</label>
                            <input type="date" id="hasta" onchange="handleInput()"
                                class="form-control form-control-sm"
                                value="{{ request('hasta', Carbon\Carbon::now()->toDateString()) }}">
                        </div>
                    </div>

                    <div class="d-flex flex-column mb-2">
                        <input type="number" id="numeroBuscar" class="form-control shadow-sm mb-1" placeholder="N°"
                            style="border-radius:6px; height:38px; font-size:14px;" />
                        <button type="button" class="btn btn-outline-primary w-100" id="btnBuscarOrden"
                            style="height:38px; font-size:14px;">Revisar</button>
                    </div>

                    {{--
                    <a href="#" class="btn-as-panel text-muted" style="cursor:not-allowed;" title="Requiere ID de orden">
                        <span class="icon" style="background: linear-gradient(90deg,#9ca3af,#d1d5db)">⏳</span>
                        Rep 2 (Inactivo)
                    </a>
                    --}}

                    <a href="{{ route('orden.reponer') }}" class="btn-as-panel">
                        <span class="icon" style="background: linear-gradient(90deg,#10b981,#34d399)">♻️</span>
                        Reponer
                    </a>

                    <a href="{{ route('informe.detallado') }}" id="btnInformeDetallado"
                        class="btn-as-panel w-100 text-center">
                        <span class="icon" style="background: linear-gradient(90deg,#06b6d4,#3b82f6)">🔗</span>
                        Informe detallado
                    </a>

                    <a href="{{ route('compras.proveedor') }}" id="btnComprasProveedor" class="btn-as-panel">
                        <span class="icon" style="background: linear-gradient(90deg,#06b6d4,#10b981)">🏷️</span>
                        Compras proveedor
                    </a>

                    <a href="{{ route('resumen.proveedor') }}" id="btnResumenProveedor" class="btn-as-panel">
                        <span class="icon" style="background: linear-gradient(90deg,#f59e0b,#06b6d4)">📊</span>
                        Resumen proveedor
                    </a>

                    <a href="{{ route('informe') }}" id="btnInforme" class="btn-as-panel">
                        <span class="icon" style="background: linear-gradient(90deg,#6366f1,#06b6d4)">📄</span>
                        Informe
                    </a>

                    <a href="{{ route('transparencia') }}" class="btn-as-panel">
                        <span class="icon" style="background: linear-gradient(90deg,#ef4444,#06b6d4)">🔎</span>
                        Transparencia
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Agregar fila
        function agregarFila() {
            const tbody = document.querySelector('#itemsTable tbody');
            const fila = document.createElement('tr');
            fila.innerHTML = `
        <td><input type="number" name="cantidad[]" min="0" step="1" class="form-control form-control-sm no-arrows qty" /></td>
        <td class="d-flex align-items-center">
            <input type="text" name="descripcion[]" class="form-control form-control-sm desc me-1" placeholder="Descripción del artículo" />
            <input type="checkbox" name="aplica_desc[]" class="form-check-input small-checkbox" title="Aplica descuento?" />
        </td>
        <td><input type="text" name="unidad[]" class="form-control form-control-sm unidad" /></td>
        <td><input type="number" name="precio_unitario[]" step="0.01" class="form-control form-control-sm no-arrows price" /></td>
        <td><input type="number" name="descuento[]" step="0.01" class="form-control form-control-sm no-arrows discount" /></td>
        <td><input type="text" name="valor[]" class="valor-read" readonly value="0.00" /></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-danger py-0 px-2" onclick="eliminarFila(this)">X</button></td>
    `;
            tbody.appendChild(fila);
            agregarListeners();
        }

        // Eliminar fila
        function eliminarFila(boton) {
            const tbody = document.querySelector('#itemsTable tbody');
            if (tbody.rows.length > 1) {
                boton.closest('tr').remove();
                calcularTotales();
            }
        }

        // Salir
        function salir() {
            window.location.href = "{{ url('/') }}";
        }

        // Cálculos automáticos
        function calcularFila(row) {
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const precio = parseFloat(row.querySelector('.price').value) || 0;
            const desc = parseFloat(row.querySelector('.discount').value) || 0;
            const aplicaDesc = row.querySelector('.small-checkbox').checked;
            let valor = qty * precio;
            if (aplicaDesc) valor -= desc;
            row.querySelector('.valor-read').value = valor.toFixed(2);
            return valor;
        }

        function calcularTotales() {
            const rows = document.querySelectorAll('#itemsTable tbody tr');
            let subTotal = 0;
            let totalDesc = 0;
            rows.forEach(row => {
                const qty = parseFloat(row.querySelector('.qty').value) || 0;
                const precio = parseFloat(row.querySelector('.price').value) || 0;
                const desc = parseFloat(row.querySelector('.discount').value) || 0;
                const aplicaDesc = row.querySelector('.small-checkbox').checked;
                const valor = calcularFila(row);
                subTotal += qty * precio;
                if (aplicaDesc) totalDesc += desc;
            });
            const impuesto = (subTotal - totalDesc) * 0.15;
            const total = subTotal - totalDesc + impuesto;

            document.getElementById('subTotal').innerText = subTotal.toFixed(2);
            document.getElementById('descTotal').innerText = totalDesc.toFixed(2);
            document.getElementById('impuesto').innerText = impuesto.toFixed(2);
            document.getElementById('total').innerText = total.toFixed(2);

            document.getElementById('subTotalInput').value = subTotal.toFixed(2);
            document.getElementById('descTotalInput').value = totalDesc.toFixed(2);
            document.getElementById('impuestoInput').value = impuesto.toFixed(2);
            document.getElementById('totalInput').value = total.toFixed(2);
        }

        function agregarListeners() {
            document.querySelectorAll('.qty, .price, .discount, .small-checkbox').forEach(input => {
                input.oninput = calcularTotales;
                input.onchange = calcularTotales;
            });
        }

        // Inicializar 7 filas
        window.addEventListener('DOMContentLoaded', () => {
            for (let i = 0; i < 7; i++) agregarFila();

            // Autocomplete Proveedor
            setupAutocomplete(
                document.getElementById('proveedor'),
                document.getElementById('listaProveedores'),
                '{{ route('api.proveedores') }}',
                function(item) {
                    document.getElementById('proveedor').value = item.nombre;
                    if (item.direccion) document.getElementById('lugar').value = item.direccion;
                }
            );

            // Autocomplete Usuarios
            setupAutocomplete(
                document.getElementById('solicitado'),
                document.getElementById('listaUsuarios'),
                '{{ route('api.usuarios') }}',
                function(item) {
                    document.getElementById('solicitado').value = item.name;
                }
            );
        });

        // ==========================
        //  LÓGICA AUTOCOMPLETE
        // ==========================
        function setupAutocomplete(input, list, url, onSelect) {
            let timeout = null;
            let currentFocus = -1;

            input.addEventListener('input', function() {
                const val = this.value;
                if (!val) {
                    list.style.display = 'none';
                    return;
                }
                clearTimeout(timeout);

                timeout = setTimeout(() => {
                    fetch(`${url}?q=${val}`)
                        .then(res => res.json())
                        .then(data => {
                            list.innerHTML = '';
                            currentFocus = -1;
                            if (data.length > 0) {
                                list.style.display = 'block';
                                data.forEach(item => {
                                    const div = document.createElement('div');
                                    div.classList.add('autocomplete-item');
                                    // Muestra nombre y un extra si existe (ej. telefono o email)
                                    div.innerHTML =
                                        `<strong>${item.nombre || item.name}</strong>`;
                                    div.addEventListener('click', function() {
                                        onSelect(item);
                                        list.style.display = 'none';
                                    });
                                    list.appendChild(div);
                                });
                            } else {
                                list.style.display = 'none';
                            }
                        });
                }, 300); // Espera 300ms al escribir
            });

            // Navegación con Teclado
            input.addEventListener('keydown', function(e) {
                let items = list.querySelectorAll('.autocomplete-item');
                if (e.key === 'ArrowDown') {
                    currentFocus++;
                    addActive(items);
                } else if (e.key === 'ArrowUp') {
                    currentFocus--;
                    addActive(items);
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (currentFocus > -1 && items) {
                        items[currentFocus].click();
                    }
                } else if (e.key === 'Tab') {
                    if (currentFocus > -1 && items) {
                        items[currentFocus].click();
                    } else {
                        list.style.display = 'none';
                    }
                }
            });

            function addActive(items) {
                if (!items) return false;
                removeActive(items);
                if (currentFocus >= items.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = items.length - 1;
                items[currentFocus].classList.add('active');
                items[currentFocus].scrollIntoView({
                    block: 'nearest'
                });
            }

            function removeActive(items) {
                for (let i = 0; i < items.length; i++) {
                    items[i].classList.remove('active');
                }
            }

            // Cerrar si clic fuera
            document.addEventListener('click', function(e) {
                if (e.target !== input) {
                    list.style.display = 'none';
                }
            });
        }

        // BUSCAR ORDEN POR ID (Botón Revisar)
        document.getElementById('btnBuscarOrden').addEventListener('click', function() {
            var num = document.getElementById('numeroBuscar').value;
            if (num.trim() !== '') {
                // Redirige a /orden/espera/{id}
                window.location.href = "{{ url('/orden/espera') }}/" + num;
            } else {
                alert('Por favor ingrese un número de orden.');
            }
        });

        // Actualizar enlace de informe con fechas
        // Actualizar enlaces de informe con fechas
        function handleInput() {
            const desde = document.getElementById('desde').value;
            const hasta = document.getElementById('hasta').value;

            const btnDetallado = document.getElementById('btnInformeDetallado');
            const btnCompras = document.getElementById('btnComprasProveedor');
            const btnResumen = document.getElementById('btnResumenProveedor');
            const btnInforme = document.getElementById('btnInforme');

            const params = `?desde=${desde}&hasta=${hasta}`;

            if (btnDetallado) btnDetallado.href = "{{ route('informe.detallado') }}" + params;
            if (btnCompras) btnCompras.href = "{{ route('compras.proveedor') }}" + params;
            if (btnResumen) btnResumen.href = "{{ route('resumen.proveedor') }}" + params;
            if (btnInforme) btnInforme.href = "{{ route('informe') }}" + params;
        }

        // Inicializar enlace al cargar
        document.addEventListener('DOMContentLoaded', handleInput);
    </script>
</body>

</html>
