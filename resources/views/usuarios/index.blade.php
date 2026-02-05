<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    @include('partials.navbar')
    <div class="container py-4 mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">👥 Usuarios (Solicitantes)</h2>
            <div>
                {{-- <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary me-2">🏠 Inicio</a> --}}
                <button class="btn btn-primary" onclick="abrirModal()">+ Nuevo Usuario</button>
            </div>
        </div>

        <!-- BUSCADOR -->
        <form action="{{ route('usuarios.index') }}" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Buscar por nombre o correo..."
                    value="{{ request('q') }}">
                <button class="btn btn-outline-primary" type="submit">Buscar</button>
            </div>
        </form>

        <!-- ALERTA -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- TABLA -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Nombre (Solicitante)</th>
                            <th>Correo Electrónico</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                            <tr>
                                <td>{{ $usuario->name }}</td>
                                <td>{{ $usuario->email }}</td>
                                <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="editarUsuario({{ $usuario->id }}, '{{ $usuario->name }}', '{{ $usuario->email }}')">
                                        ✏️ Editar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">No se encontraron usuarios.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $usuarios->links() }}
        </div>
    </div>

    <!-- MODAL USUARIO -->
    <div class="modal fade" id="modalUsuario" tabindex="-1">
        <div class="modal-dialog">
            <form id="formUsuario" action="{{ route('usuarios.store') }}" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalTitle">Nuevo Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Identidad</label>
                            <input type="text" name="identidad" id="userIdentidad" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" name="nombre" id="userNombre" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Nombre de Usuario</label>
                            <input type="text" name="name" id="userName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Correo Electrónico</label>
                            <input type="email" name="email" id="userEmail" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Contraseña <small class="text-muted" id="passHelp">(Obligatoria)</small></label>
                            <input type="password" name="password" id="userPass" class="form-control">
                            <small class="text-danger d-none" id="passEditMsg">* Dejar en blanco para mantener la
                                actual</small>
                        </div>
                        <div class="mb-3">
                            <label>Tipo / Nivel</label>
                            <select name="nivel" id="userNivel" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <option value="super_admin">Super Admin</option>
                                <option value="admin">Administrador</option>
                                <option value="estandar">Estándar</option>
                            </select>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="activo" id="userActivo" checked>
                            <label class="form-check-label" for="userActivo">
                                Usuario Activo
                            </label>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
        const form = document.getElementById('formUsuario');
        const title = document.getElementById('modalTitle');
        const methodField = document.getElementById('methodField');
        const passHelp = document.getElementById('passHelp');
        const passEditMsg = document.getElementById('passEditMsg');
        const passInput = document.getElementById('userPass');

        // Campos para autofill
        const identidadInput = document.getElementById('userIdentidad');
        const nombreInput = document.getElementById('userNombre'); // Readonly
        const userNameInput = document.getElementById('userName');
        const userEmailInput = document.getElementById('userEmail');

        // Lógica de Autocompletado RRHH
        identidadInput.addEventListener('blur', async function() {
            const identidad = this.value;
            if (identidad.length < 5) return; // Evitar búsquedas cortas

            try {
                // Mostrar estado de carga
                nombreInput.value = "Buscando...";

                const response = await fetch(`{{ route('api.rrhh.empleado') }}?identidad=${identidad}`);
                const data = await response.json();

                if (data.success) {
                    // Llenar campos
                    nombreInput.value = data.data.name; // Nombre real (RRHH)
                    userNameInput.value = data.data.username; // Sugerir como nombre de usuario (Aleatorio)

                    // Generar correo sugerido: nombre.apellido@sistema.local
                    // Limpiamos acentos y caracteres especiales para el email
                    const cleanName = data.data.name.toLowerCase()
                        .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
                        .replace(/[^a-z0-9\s]/g, "")
                        .split(" ");

                    // Primer nombre + primer apellido (simple logic)
                    if (cleanName.length >= 2) {
                        const emailSuggested = `${cleanName[0]}.${cleanName[2] || cleanName[1]}@sistema.local`;
                        userEmailInput.value = emailSuggested;
                    }

                    // Visual feedback
                    identidadInput.classList.add('is-valid');
                    identidadInput.classList.remove('is-invalid');
                } else {
                    nombreInput.value = ""; // Limpiar si no encuentra
                    identidadInput.classList.add('is-invalid');
                    identidadInput.classList.remove('is-valid');
                    // Opcional: Mostrar toast o small text
                }
            } catch (error) {
                console.error('Error fetching RRHH:', error);
                nombreInput.value = "Error de conexión";
            }
        });

        function abrirModal() {
            form.action = "{{ route('usuarios.store') }}";
            methodField.innerHTML = '';
            title.innerText = "Nuevo Usuario";

            identidadInput.value = "";
            identidadInput.classList.remove('is-valid', 'is-invalid');
            nombreInput.value = "";

            document.getElementById('userName').value = "";
            document.getElementById('userEmail').value = "";
            passInput.value = "";
            passInput.required = true;
            passHelp.innerText = "(Obligatoria)";
            passEditMsg.classList.add('d-none');

            modal.show();
        }

        function editarUsuario(id, name, email) {
            form.action = "{{ url('/usuarios') }}/" + id;
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            title.innerText = "Editar Usuario";

            // En editar, quizás no queremos obligar a buscar por identidad de nuevo si ya existe,
            // pero limpiamos los campos visuales de búsqueda
            identidadInput.value = "";
            nombreInput.value = "";

            document.getElementById('userName').value = name;
            document.getElementById('userEmail').value = email;

            passInput.value = "";
            passInput.required = false;
            passHelp.innerText = "(Opcional)";
            passEditMsg.classList.remove('d-none');

            modal.show();
        }
    </script>
</body>

</html>
