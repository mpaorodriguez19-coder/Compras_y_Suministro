<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Bitácora del Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    @include('partials.navbar') <!-- Ensure this includes the admin navbar/logout logic -->

    <div class="container py-4 mt-5">
        <h2 class="fw-bold mb-4">🛡️ Bitácora de Auditoría</h2>

        <!-- FILTROS -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('bitacora.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Usuario</label>
                        <input type="text" name="user" class="form-control" placeholder="Buscar usuario..."
                            value="{{ request('user') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Módulo</label>
                        <select name="modulo" class="form-select">
                            <option value="">Todos</option>
                            @foreach ($modulos as $mod)
                                <option value="{{ $mod }}" {{ request('modulo') == $mod ? 'selected' : '' }}>
                                    {{ $mod }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Desde</label>
                        <input type="date" name="fecha_desde" class="form-control"
                            value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control"
                            value="{{ request('fecha_hasta') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABLA -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 font-sm">
                    <thead class="bg-light">
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Usuario</th>
                            <th>Tipo</th>
                            <th>Módulo</th>
                            <th>Acción</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bitacoras as $log)
                            <tr>
                                <td class="text-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="fw-bold">{{ $log->user_name ?? 'Sistema' }}</td>
                                <td>
                                    @if (str_contains($log->user_type, 'Admin'))
                                        <span class="badge bg-danger">Admin</span>
                                    @elseif(str_contains($log->user_type, 'User'))
                                        <span class="badge bg-primary">Usuario</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-info text-dark">{{ $log->modulo }}</span></td>
                                <td>{{ $log->accion }}</td>
                                <td class="small text-muted">{{ $log->ip_address }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No hay registros en la bitácora.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $bitacoras->links() }}
        </div>
    </div>
</body>

</html>
