<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Municipalidad de Danlí</title>
    <!-- FIRST RUN SETUP MODAL -->
    @if (isset($needsSetup) && $needsSetup)
        <div class="modal fade" id="modalSetup" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('setup.firstAdmin') }}" method="POST">
                        @csrf
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">⚠️ Configuración Inicial Requerida</h5>
                        </div>
                        <div class="modal-body">
                            <p class="text-danger fw-bold">No se ha detectado ningún Administrador.</p>
                            <p>Para asegurar el sistema, debe crear una cuenta de <strong>Super Administrador</strong>
                                ahora mismo.</p>
                            <p class="small text-muted">Se descargará un archivo con estos datos automáticamente.</p>

                            <div class="mb-3">
                                <label>Nombre Completo</label>
                                <input type="text" name="name" class="form-control" required
                                    placeholder="Ej: Administrador Principal">
                            </div>
                            <div class="mb-3">
                                <label>Correo Electrónico</label>
                                <input type="email" name="email" class="form-control" required
                                    placeholder="admin@sistema.local">
                            </div>
                            <div class="mb-3">
                                <label>Contraseña</label>
                                <input type="password" name="password" class="form-control" required minlength="6">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger w-100">Crear Super Admin y Acceder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var myModal = new bootstrap.Modal(document.getElementById('modalSetup'));
                myModal.show();
            });
        </script>
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #0ea5a4 0%, #06b6d4 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-login {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }

        .card-header {
            background: transparent;
            border-bottom: none;
            text-align: center;
            padding-top: 30px;
            padding-bottom: 0;
        }

        .icon-lock {
            font-size: 3rem;
            color: #0ea5a4;
            margin-bottom: 10px;
        }

        .btn-primary {
            background-color: #0ea5a4;
            border-color: #0ea5a4;
        }

        .btn-primary:hover {
            background-color: #0c8a89;
            border-color: #0c8a89;
        }
    </style>
</head>

<body>

    <div class="card card-login p-4">
        <div class="card-header">
            <div class="icon-lock">🔒</div>
            <h4 class="fw-bold text-secondary">Acceso al Sistema</h4>
            <p class="text-muted small">Compras y Suministros</p>
        </div>
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold small text-secondary">CORREO ELECTRÓNICO</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') }}" required autofocus placeholder="admin@sistema.com">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-bold small text-secondary">CONTRASEÑA</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" required placeholder="••••••••">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label small" for="remember">Recordarme</label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2 fw-bold">INGRESAR</button>
                    <a href="{{ route('password.request') }}"
                        class="btn btn-link btn-sm text-secondary text-decoration-none">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
            </form>
        </div>
        <div class="card-footer text-center bg-white border-0 pb-3">
            <small class="text-muted">© {{ date('Y') }} Municipalidad de Danlí</small>
        </div>
    </div>

</body>

</html>
