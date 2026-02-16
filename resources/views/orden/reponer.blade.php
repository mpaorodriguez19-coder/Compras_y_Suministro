<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reponer Orden de Compra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            padding: 20px;
        }

        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 10px;
        }

        .table thead th {
            background-color: #e9ecef;
            border-bottom: 2px solid #dee2e6;
        }

        .btn-remove {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }

        .totals-card {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card p-4">
            <h4 class="text-center mb-4 text-primary fw-bold">REPONER ORDEN DE COMPRA</h4>

            {{-- Mensajes de Error/Éxito --}}
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('orden.reponer.guardar') }}" id="reponerForm">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Fecha</label>
                        <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-bold">Proveedor</label>
                        <select name="proveedor" class="form-control" required>
                            <option value="">-- Seleccione un Proveedor --</option>
                            @foreach ($proveedores as $proveedor)
                                @php
                                    $value = $proveedor->nombre . ($proveedor->rtn ? ' - ' . $proveedor->rtn : '');
                                    $selected = '';
                                    if (isset($ordenOrigen) && $ordenOrigen->proveedor_id == $proveedor->id) {
                                        $selected = 'selected';
                                    }
                                @endphp
                                <option value="{{ $value }}" {{ $selected }}>
                                    {{ $proveedor->nombre }} @if ($proveedor->rtn)
                                        ({{ $proveedor->rtn }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Lugar</label>
                        <input type="text" name="lugar" class="form-control"
                            value="{{ isset($ordenOrigen) ? $ordenOrigen->lugar : '' }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Solicitado por</label>
                        <input type="text" name="solicitado" class="form-control"
                            value="{{ isset($ordenOrigen) && $ordenOrigen->solicitante ? $ordenOrigen->solicitante->name : '' }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Concepto</label>
                        <textarea name="concepto" class="form-control" rows="2">{{ isset($ordenOrigen) ? $ordenOrigen->concepto : '' }}</textarea>
                    </div>
                </div>

                <hr>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle" id="itemsTable">
                        <thead class="text-center">
                            <tr>
                                <th style="width: 100px;">Cant</th>
                                <th>Descripción</th>
                                <th style="width: 100px;">Unidad</th>
                                <th style="width: 120px;">Precio U.</th>
                                <th style="width: 120px;">Valor</th>
                                <th style="width: 60px;">X</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Las filas se generan con JS --}}
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary btn-sm" onclick="agregarFila()">+ Agregar
                        Ítem</button>
                </div>

                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <div class="totals-card">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="spanSubtotal" class="fw-bold">0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 align-items-center">
                                <span>Descuento:</span>
                                <input type="number" name="descuento_total" id="inputDescuento"
                                    class="form-control form-control-sm text-end" style="width: 100px;" step="0.01"
                                    value="{{ isset($ordenOrigen) ? $ordenOrigen->descuento : 0 }}"
                                    oninput="calcularTotales()">
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Impuesto (15%):</span>
                                <span id="spanImpuesto">0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold fs-5">Total:</span>
                                <span id="spanTotal" class="fw-bold fs-5 text-success">0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4 mb-5">
                    <button type="submit" class="btn btn-success px-5 py-2 fw-bold">💾 Guardar y Generar PDF</button>
                    <a href="{{ route('ordenes.index') }}" class="btn btn-secondary px-4 py-2">Cancelar</a>
                </div>

            </form>
        </div>
    </div>

    <script>
        // Datos iniciales (si vienen de una orden copiada)
        const initialItems = @json(isset($ordenOrigen) && $ordenOrigen->items->count() > 0 ? $ordenOrigen->items : []);

        document.addEventListener('DOMContentLoaded', () => {
            if (initialItems.length > 0) {
                initialItems.forEach(item => {
                    agregarFila(item.cantidad, item.descripcion, item.unidad, item.precio_unitario);
                });
            } else {
                // Agregar al menos una fila vacía
                agregarFila();
            }
            calcularTotales();
        });

        function agregarFila(cant = '', desc = '', unid = '', prec = '') {
            const tbody = document.querySelector('#itemsTable tbody');
            const row = document.createElement('tr');

            row.innerHTML = `
                <td>
                    <input type="number" name="cantidad[]" class="form-control qty" step="0.01" value="${cant}" oninput="calcularTotales()" required>
                </td>
                <td>
                    <input type="text" name="descripcion[]" class="form-control" value="${desc}" required>
                    <div class="form-check mt-1">
                        <input class="form-check-input tax-check" type="checkbox" value="1" name="aplica_impuesto[]" onchange="calcularTotales()">
                        <label class="form-check-label" style="font-size: 0.8rem;">+ ISV</label>
                        {{-- Hidden input para asegurar que se envía array si necesitamos mapear índices exactos, 
                             pero 'aplica_impuesto' como array de checkbox solo envía los checked. 
                             Para simplificar, asumiremos que si marcan check, aplica global o por item logic.
                             Mejor: hidden input with 0/1 driven by checkbox --}}
                        <input type="hidden" name="aplica_impuesto_val[]" class="tax-val" value="0">
                    </div>
                </td>
                <td>
                    <input type="text" name="unidad[]" class="form-control" value="${unid}">
                </td>
                <td>
                    <input type="number" name="precio_unitario[]" class="form-control price" step="0.01" value="${prec}" oninput="calcularTotales()" required>
                </td>
                <td class="text-end">
                    <input type="text" class="form-control-plaintext text-end row-total fw-bold" readonly value="0.00">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-remove btn-sm" onclick="eliminarFila(this)">X</button>
                </td>
            `;

            tbody.appendChild(row);

            // Hack para el checkbox val
            const checkbox = row.querySelector('.tax-check');
            const hiddenVal = row.querySelector('.tax-val');
            checkbox.addEventListener('change', () => {
                hiddenVal.value = checkbox.checked ? 1 : 0;
                // Necesitamos asegurar que el array de hidden inputs se llame 'aplica_impuesto[]' con valores 0 o 1
                // Pero el backend espera 'aplica_impuesto' que coincida con el índice...
                // Si usamos checkbox directo, los indices se pierden si no se marcan todos.
                // SOLUCION: El name del hidden será 'aplica_impuesto[]' y el checkbox solo controla el valor.
                hiddenVal.name = 'aplica_impuesto[]';
            });
            // Init hidden name correctly
            hiddenVal.name = 'aplica_impuesto[]';

            calcularTotales();
        }

        function eliminarFila(btn) {
            const row = btn.closest('tr');
            row.remove();
            calcularTotales();
        }

        function calcularTotales() {
            let subtotal = 0;
            let impuesto = 0;

            document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
                const qty = parseFloat(row.querySelector('.qty').value) || 0;
                const price = parseFloat(row.querySelector('.price').value) || 0;
                const total = qty * price;

                row.querySelector('.row-total').value = total.toFixed(2);

                subtotal += total;

                // Check impuesto
                if (row.querySelector('.tax-check').checked) {
                    impuesto += total * 0.15;
                }
            });

            const descuento = parseFloat(document.getElementById('inputDescuento').value) || 0;
            const total = subtotal + impuesto - descuento;

            document.getElementById('spanSubtotal').innerText = subtotal.toFixed(2);
            document.getElementById('spanImpuesto').innerText = impuesto.toFixed(2);
            document.getElementById('spanTotal').innerText = total.toFixed(2);
        }
    </script>
</body>

</html>
