<nav class="bg-white shadow-sm py-2 mb-3 fixed-top">
    <div class="container d-flex justify-content-between align-items-center">
        <!-- Izquierda: Botón Panel Principal o Título -->
        <div class="d-flex align-items-center gap-3">
            @if (Route::currentRouteName() != 'dashboard')
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    🏠 Ir al Panel Principal
                </a>
            @else
                <span class="fw-bold text-dark fs-5">🏢 Compras y Suministros</span>
            @endif
        </div>

        <!-- Derecha: Dropdown Usuario -->
        <div class="dropdown">
            @php
                $currentUser = Auth::guard('admin')->user() ?? Auth::guard('web')->user();
            @endphp
            <button class="btn btn-light border dropdown-toggle d-flex align-items-center gap-2" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <span class="fw-bold text-dark small">{{ $currentUser->name ?? 'Usuario invitado' }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 200px;">
                <li>
                    <h6 class="dropdown-header">
                        {{ $currentUser->email ?? '' }}
                    </h6>
                </li>
                <li>
                    <div class="px-3 pb-2">
                        <span class="badge bg-info text-dark">
                            @if (isset($currentUser->role) && $currentUser->role == 'super_admin')
                                Super Admin
                            @elseif(isset($currentUser->role) && $currentUser->role == 'admin')
                                Administrador
                            @else
                                {{ ucfirst($currentUser->tipo ?? 'Usuario') }}
                            @endif
                        </span>
                    </div>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                @if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->role == 'super_admin')
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('bitacora.index') }}">
                            🛡️ Bitácora
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2"
                            href="{{ route('configuracion.index') }}">
                            ⚙️ Configuración
                        </a>
                    </li>
                @endif
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                            🚪 Cerrar Sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Bootstrap Bundle JS (Includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Tiempo de inactividad en milisegundos (120 minutos = 7,200,000 ms) - Coincide con SESSION_LIFETIME=.env
    const INACTIVITY_LIMIT = 7200000;
    let inactivityTimer;

    // Control de Ping (Keep-alive)
    let lastPingTime = 0;
    const PING_INTERVAL = 300000; // 5 minutos (300,000 ms)

    function resetTimer() {
        // 1. Reiniciar contador local de logout
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(forceLogout, INACTIVITY_LIMIT);

        // 2. Enviar Ping al servidor si ha pasado el intervalo (para mantener sesión PHP activa)
        const now = Date.now();
        if (now - lastPingTime > PING_INTERVAL) {
            lastPingTime = now;
            // Usamos fetch para golpear la ruta que renueva la sesión
            fetch("{{ route('api.checkSessionActivity') }}", {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }).catch(err => console.warn('Error manteniendo sesión activa:', err));
        }
    }

    function forceLogout() {
        // Opción 1: Enviar formulario de logout automáticamente
        // document.querySelector('form[action="{{ route('logout') }}"]').submit();

        // Opción 2: Redirigir directamente al login (la sesión expira en server también)
        window.location.href = "{{ route('login') }}";
    }

    // Eventos que reinician el contador
    window.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;
    document.onclick = resetTimer;
    document.onscroll = resetTimer;
</script>
