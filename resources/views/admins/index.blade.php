<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Administradores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    @include('partials.navbar')
    <div class="container py-4 mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-danger">👮‍♂️ Administradores del Sistema</h2>
            <div>
                {{-- <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary me-2">🏠 Inicio</a> --}}
                <button class="btn btn-danger" onclick="abrirModal()">+ Nuevo Admin</button>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Fecha Creación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $admin)
                            <tr>
                                <td class="fw-bold">{{ $admin->name }}</td>
                                <td>{{ $admin->email }}</td>
                                <td>
                                    @if ($admin->role === 'super_admin')
                                        <span class="badge bg-danger">SUPER ADMIN</span>
                                    @else
                                        <span class="badge bg-secondary">Admin</span>
                                    @endif
                                </td>
                                <td>{{ $admin->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if ($admin->role !== 'super_admin')
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="editarAdmin({{ $admin->id }}, '{{ $admin->name }}', '{{ $admin->email }}')">
                                            ✏️ Editar
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No hay administradores.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $admins->links() }}</div>
    </div>

    <!-- MODAL -->
    <div class="modal fade" id="modalAdmin" tabindex="-1">
        <div class="modal-dialog">
            <form id="formAdmin" action="{{ route('admins.store') }}" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalTitle">Nuevo Administrador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small text-muted">Este usuario podrá gestionar órdenes y proveedores, pero NO podrá
                            crear otros admins ni usuarios.</p>
                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" name="name" id="adminName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Correo</label>
                            <input type="email" name="email" id="adminEmail" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Contraseña <small class="text-muted" id="passHelp">(Obligatoria)</small></label>
                            <input type="password" name="password" id="adminPass" class="form-control">
                            <small class="text-danger d-none" id="passEditMsg">* Dejar en blanco para mantener la
                                actual</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = new bootstrap.Modal(document.getElementById('modalAdmin'));
        const form = document.getElementById('formAdmin');
        const title = document.getElementById('modalTitle');
        const methodField = document.getElementById('methodField');
        const passHelp = document.getElementById('passHelp');
        const passEditMsg = document.getElementById('passEditMsg');
        const passInput = document.getElementById('adminPass');

        function abrirModal() {
            form.action = "{{ route('admins.store') }}";
            methodField.innerHTML = '';
            title.innerText = "Nuevo Administrador";

            document.getElementById('adminName').value = "";
            document.getElementById('adminEmail').value = "";
            passInput.value = "";
            passInput.required = true;
            passHelp.innerText = "(Obligatoria)";
            passEditMsg.classList.add('d-none');

            modal.show();
        }

        function editarAdmin(id, name, email) {
            form.action = "{{ url('/admins') }}/" + id;
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            title.innerText = "Editar Administrador";

            document.getElementById('adminName').value = name;
            document.getElementById('adminEmail').value = email;

            passInput.value = "";
            passInput.required = false;
            passHelp.innerText = "(Opcional)";
            passEditMsg.classList.remove('d-none');

            modal.show();
        }
    </script>
</body>

</html>
