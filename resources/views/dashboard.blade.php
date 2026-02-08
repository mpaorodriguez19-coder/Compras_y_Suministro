<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - Compras y Suministros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f4f6;
        }

        .card-dashboard {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            height: 100%;
        }

        .card-dashboard:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            color: inherit;
        }

        .icon-large {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    @include('partials.navbar')

    <div class="container py-5 mt-5">

        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-dark">Compras y Suministros</h1>
            <p class="lead text-muted">Panel de Gestión Municipal</p>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- CREAR ORDEN -->
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('ordenes.index') }}" class="card card-dashboard border-0 shadow-sm text-center p-4">
                    <div class="card-body">
                        <div class="icon-large">📦</div>
                        <h4 class="card-title fw-bold">Crear Orden</h4>
                        <p class="card-text text-muted">Generar nueva orden de compra o suministro.</p>
                    </div>
                </a>
            </div>

            <!-- PROVEEDORES -->
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('proveedores.index') }}"
                    class="card card-dashboard border-0 shadow-sm text-center p-4">
                    <div class="card-body">
                        <div class="icon-large">🏢</div>
                        <h4 class="card-title fw-bold">Proveedores</h4>
                        <p class="card-text text-muted">Gestionar lista, editar RTN, teléfonos y contactos.</p>
                    </div>
                </a>
            </div>

            <!-- USUARIOS -->
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('usuarios.index') }}" class="card card-dashboard border-0 shadow-sm text-center p-4">
                    <div class="card-body">
                        <div class="icon-large">👥</div>
                        <h4 class="card-title fw-bold">Usuarios</h4>
                        <p class="card-text text-muted">Administrar solicitantes y departamentos.</p>
                    </div>
                </a>
            </div>

            <!-- Tarjeta 4: Administradores (Solo Super Admin) -->

            <div class="col-md-4 col-lg-3">
                <a href="{{ route('admins.index') }}" class="card card-dashboard border-0 shadow-sm text-center p-4">
                    <div class="card-body">
                        <div class="icon-large text-danger">👮‍♂️</div>
                        <h4 class="card-title fw-bold">Administradores</h4>
                        <p class="card-text text-muted">Crear nuevos admins</p>
                    </div>
                </a>
            </div>

            <!-- CONFIGURACIÓN Y RESPALDO -->
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('backup.index') }}" class="card card-dashboard border-0 shadow-sm text-center p-4">
                    <div class="card-body">
                        <div class="icon-large text-secondary">⚙️</div>
                        <h4 class="card-title fw-bold">Configuración</h4>
                        <p class="card-text text-muted">Respaldos y Restauración</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="text-center mt-5 text-muted small">
            &copy; {{ date('Y') }} Municipalidad de Danlí
        </div>
    </div>
</body>

</html>
