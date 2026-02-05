<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Proveedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    @include('partials.navbar')
    <div class="container py-4 mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">🏢 Proveedores</h2>
            <div>
                {{-- <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary me-2">🏠 Inicio</a> --}}
                <button class="btn btn-primary" onclick="abrirModal()">+ Nuevo Proveedor</button>
            </div>
        </div>

        <!-- BUSCADOR -->
        <form action="{{ route('proveedores.index') }}" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Buscar por nombre o RTN..."
                    value="{{ request('q') }}">
                <button class="btn btn-outline-primary" type="submit">Buscar</button>
            </div>
        </form>

        <!-- ALERTA -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var modal = new bootstrap.Modal(document.getElementById('modalProveedor'));
                    modal.show();
                });
            </script>
        @endif

        <!-- TABLA -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Nombre</th>
                            <th>RTN</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Dirección</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proveedores as $proveedor)
                            <tr>
                                <td class="fw-bold">{{ $proveedor->nombre }}</td>
                                <td>{{ $proveedor->rtn ?? '-' }}</td>
                                <td>{{ $proveedor->telefono ?? '-' }}</td>
                                <td>{{ $proveedor->correo ?? '-' }}</td>
                                <td class="small">{{ Str::limit($proveedor->direccion, 30) }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick='editar(@json($proveedor))'>✏️</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No se encontraron proveedores.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $proveedores->links() }}
        </div>
    </div>

    <!-- MODAL CREAR/EDITAR -->
    <div class="modal fade" id="modalProveedor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('proveedores.store') }}" method="POST" id="formProveedor">
                @csrf
                <div id="methodUpdate"></div>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Nuevo Proveedor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" name="nombre" id="nombre"
                                class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}"
                                required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>RTN</label>
                                <input type="text" name="rtn" id="rtn"
                                    class="form-control @error('rtn') is-invalid @enderror" required minlength="14"
                                    maxlength="14" value="{{ old('rtn') }}">
                                @error('rtn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Teléfono</label>
                                <input type="text" name="telefono" id="telefono"
                                    class="form-control @error('telefono') is-invalid @enderror"
                                    value="{{ old('telefono') }}" required>
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Correo</label>
                            <input type="email" name="correo" id="correo"
                                class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo') }}"
                                required>
                            @error('correo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label>Dirección</label>
                            <textarea name="direccion" id="direccion" class="form-control @error('direccion') is-invalid @enderror" rows="2"
                                required>{{ old('direccion') }}</textarea>
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modal = new bootstrap.Modal(document.getElementById('modalProveedor'));
        const form = document.getElementById('formProveedor');
        const modalTitle = document.getElementById('modalTitle');
        const methodDiv = document.getElementById('methodUpdate');

        // Auto-search reset
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                if (this.value === '') {
                    this.form.submit();
                }
            });
        }

        function abrirModal() {
            form.reset();
            form.action = "{{ route('proveedores.store') }}";
            methodDiv.innerHTML = '';
            modalTitle.textContent = "Nuevo Proveedor";
            document.getElementById('nombre').value = '';
            document.getElementById('rtn').value = '';
            document.getElementById('telefono').value = '';
            document.getElementById('correo').value = '';
            document.getElementById('direccion').value = '';
            modal.show();
        }

        function editar(data) {
            form.action = `/proveedores/${data.id}`;
            methodDiv.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            modalTitle.textContent = "Editar Proveedor";

            document.getElementById('nombre').value = data.nombre;
            document.getElementById('rtn').value = data.rtn || '';
            document.getElementById('telefono').value = data.telefono || '';
            document.getElementById('correo').value = data.correo || '';
            document.getElementById('direccion').value = data.direccion || '';

            modal.show();
        }
    </script>
</body>

</html>
