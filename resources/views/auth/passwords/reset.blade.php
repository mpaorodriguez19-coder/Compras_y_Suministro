<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Municipalidad de Danlí</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
            width: 100%;
            max-width: 400px;
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
        <div class="card-header bg-transparent border-0 text-center pt-3">
            <h4 class="fw-bold text-secondary">Nueva Contraseña</h4>
            <p class="text-muted small">Establezca una nueva contraseña para su cuenta</p>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="email" class="form-label fw-bold small text-secondary">CORREO ELECTRÓNICO</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-bold small text-secondary">NUEVA CONTRASEÑA</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="new-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password-confirm" class="form-label fw-bold small text-secondary">CONFIRMAR
                        CONTRASEÑA</label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                        required autocomplete="new-password">
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary fw-bold">RESTABLECER CONTRASEÑA</button>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm">Volver al Login</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
