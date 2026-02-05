<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Lista de Órdenes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">
    @include('partials.navbar')

    <div class="container py-4 mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">📦 Lista de Órdenes</h2>
            <a href="{{ route('ordenes.index') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Nueva Orden
            </a>
        </div>

        <!-- FILTROS -->
        <div class="card shadow-sm border-0 mb-4 p-3">
            <form action="{{ route('ordenes.lista') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Buscar (Orden, Proveedor, Lugar)</label>
                    <input type="text" name="q" class="form-control" placeholder="Ej: 000123 o Juan Perez"
                        value="{{ request('q') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Desde</label>
                    <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Hasta</label>
                    <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filtrar</button>
                </div>
            </form>
        </div>

        <!-- TABLA -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Número</th>
                                <th>Proveedor</th>
                                <th>Lugar</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ordenes as $orden)
                                <tr class="{{ $orden->estado == 'anulada' ? 'table-danger' : '' }}">
                                    <td>{{ \Carbon\Carbon::parse($orden->fecha)->format('d/m/Y') }}</td>
                                    <td class="fw-bold text-danger">{{ $orden->numero }}</td>
                                    <td>{{ $orden->proveedor->nombre ?? 'N/A' }}</td>
                                    <td class="small text-muted">{{ Str::limit($orden->lugar, 20) }}</td>
                                    <td class="text-end fw-bold">{{ number_format($orden->total, 2) }} L.</td>
                                    <td class="text-center">
                                        @if ($orden->estado == 'anulada')
                                            <span class="badge bg-danger">ANULADA</span>
                                        @else
                                            <span class="badge bg-success">ACTIVA</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <!-- VER DETALLES -->
                                            <a href="{{ route('orden.espera', $orden->id) }}"
                                                class="btn btn-outline-secondary" title="Ver Detalles">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            @if ($orden->estado != 'anulada')
                                                <!-- EDITAR -->
                                                <a href="{{ route('ordenes.edit', $orden->id) }}"
                                                    class="btn btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <!-- ANULAR -->
                                                <button type="button" class="btn btn-outline-danger"
                                                    onclick="confirmarAnular({{ $orden->id }}, '{{ $orden->numero }}')"
                                                    title="Anular">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <h4 class="text-muted">No se encontraron órdenes.</h4>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0">
                {{ $ordenes->links() }}
            </div>
        </div>
    </div>

    <!-- MODAL ANULAR -->
    <div class="modal fade" id="modalAnular" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Anular Orden</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formAnular" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="lead">¿Está seguro de anular la orden <strong id="lblOrdenNumero"></strong>?</p>
                        <p class="text-muted small">Esta acción no se puede deshacer.</p>

                        <!-- CHECK DE ROL (Simplificado: El servidor valida, pero el front pide credenciales si no es super admin) -->
                        @if (auth('admin')->user()->role !== 'super_admin')
                            <div class="alert alert-warning p-2 small">
                                <i class="bi bi-lock"></i> Se requieren credenciales de <strong>Super Admin</strong>
                                para esta acción.
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Correo Super Admin</label>
                                <input type="email" name="admin_email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Contraseña</label>
                                <input type="password" name="admin_password" class="form-control" required>
                            </div>
                        @else
                            <div class="alert alert-info small">
                                <i class="bi bi-shield-check"></i> Autorizado como Super Admin.
                            </div>
                        @endif

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Confirmar Anulación</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmarAnular(id, numero) {
            const modal = new bootstrap.Modal(document.getElementById('modalAnular'));
            const form = document.getElementById('formAnular');
            const lbl = document.getElementById('lblOrdenNumero');

            form.action = `/ordenes/${id}/anular`;
            lbl.innerText = numero;

            modal.show();
        }
    </script>
</body>

</html>
