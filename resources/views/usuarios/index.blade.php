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
            <h2 class="fw-bold">👥 Usuarios </h2>
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
                            <th>Correo / DNI</th>
                            <th>Teléfono</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                            <tr>
                                <td>{{ $usuario->name }}</td>
                                <td>
                                    <small class="d-block text-muted">{{ $usuario->dni }}</small>
                                    {{ $usuario->email }}
                                </td>
                                <td>{{ $usuario->telefono ?? '-' }}</td>
                                <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"
                                        data-user='@json($usuario)' onclick="editarUsuario(this)">
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
                            <input type="text" name="dni" id="userIdentidad"
                                class="form-control @error('dni') is-invalid @enderror" required>
                            @error('dni')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Nombres</label>
                                <input type="text" name="nombres" id="userNombres"
                                    class="form-control @error('nombres') is-invalid @enderror" required>
                                @error('nombres')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Apellidos</label>
                                <input type="text" name="apellidos" id="userApellidos"
                                    class="form-control @error('apellidos') is-invalid @enderror" required>
                                @error('apellidos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Nombre de Usuario (Login)</label>
                            <input type="text" name="username" id="userUsername"
                                class="form-control @error('username') is-invalid @enderror" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label>Correo Electrónico</label>
                            <input type="email" name="email" id="userEmail"
                                class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label>Contraseña <small class="text-muted" id="passHelp">(Obligatoria)</small></label>
                            <input type="password" name="password" id="userPass"
                                class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-danger d-none" id="passEditMsg">* Dejar en blanco para mantener la
                                actual</small>
                        </div>
                        <div class="mb-3">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" id="userTelefono"
                                class="form-control @error('telefono') is-invalid @enderror">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label>Dirección</label>
                            <input type="text" name="direccion" id="userDireccion"
                                class="form-control @error('direccion') is-invalid @enderror">
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- NO USAMOS ROL/NIVEL PARA USUARIOS ESTÁNDAR, SON APARTE DE ADMINS --}}
                        {{-- 
                        <div class="mb-3">
                            <label>Tipo / Nivel</label>
                            <select name="nivel" id="userNivel" class="form-select" required> ... </select> 
                        </div> 
                        --}}

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
        const nombresInput = document.getElementById('userNombres');
        const apellidosInput = document.getElementById('userApellidos');
        const usernameInput = document.getElementById('userUsername');
        const userEmailInput = document.getElementById('userEmail');

        const userTelefonoInput = document.getElementById('userTelefono');
        const userDireccionInput = document.getElementById('userDireccion');

        // Lógica de Autocompletado RRHH cuando cambia la Identidad
        identidadInput.addEventListener('blur', async function() {
            const identidad = this.value;
            if (identidad.length < 5) return;

            // Feedback visual
            nombresInput.value = "Buscando...";
            apellidosInput.value = "Buscando...";
            identidadInput.classList.remove('is-valid', 'is-invalid');

            try {
                const response = await fetch(`/api/rrhh/empleado?identidad=${identidad}`);
                const data = await response.json();

                if (data.success) {
                    nombresInput.value = data.nombres;
                    apellidosInput.value = data.apellidos;
                    usernameInput.value = data.username;
                    userEmailInput.value = data.email_sugerido;
                    userTelefonoInput.value = data.telefono || '';
                    userDireccionInput.value = data.direccion || '';

                    // Visual feedback success
                    identidadInput.classList.add('is-valid');
                    identidadInput.classList.remove('is-invalid');
                } else {
                    nombresInput.value = "";
                    apellidosInput.value = "";
                    userTelefonoInput.value = "";
                    userDireccionInput.value = "";
                    alert(data.message || 'No encontrado en RRHH');

                    // Visual feedback error
                    identidadInput.classList.add('is-invalid');
                }
            } catch (error) {
                console.error(error);
                nombresInput.value = ""; // Limpiar error msg
                apellidosInput.value = "";
                alert('Error al consultar RRHH');
            }
        });

        // Pre-generar la URL base para el update (placeholder ID será reemplazado)
        const updateRouteBase = "{{ route('usuarios.update', ['id' => 'ID_PLACEHOLDER']) }}";

        function editarUsuario(button) {
            const user = JSON.parse(button.getAttribute('data-user'));

            // Reemplazar el placeholder con el ID real
            form.action = updateRouteBase.replace('ID_PLACEHOLDER', user.id);

            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            title.innerText = "Editar Usuario";
            passHelp.innerText = "(Opcional)";
            passEditMsg.classList.remove('d-none');
            passInput.required = false;

            // Llenar campos con los datos del objeto user
            // Nota: Para usuarios antiguos, nombres/apellidos/username pueden ser null
            document.getElementById('userIdentidad').value = user.dni || '';
            document.getElementById('userNombres').value = user.nombres || '';
            document.getElementById('userApellidos').value = user.apellidos || '';
            document.getElementById('userUsername').value = user.username || '';
            document.getElementById('userEmail').value = user.email || '';
            document.getElementById('userTelefono').value = user.telefono || '';
            document.getElementById('userDireccion').value = user.direccion || '';

            modal.show();
        }

        function abrirModal() {
            form.action = "{{ route('usuarios.store') }}";
            methodField.innerHTML = '';
            form.reset();
            title.innerText = "Nuevo Usuario";
            passHelp.innerText = "(Obligatoria)";
            passEditMsg.classList.add('d-none');
            passInput.required = true;
            modal.show();
        }
    </script>

</body>

</html>
