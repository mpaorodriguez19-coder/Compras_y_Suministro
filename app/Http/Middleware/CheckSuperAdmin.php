<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->role === 'super_admin') {
            return $next($request);
        }

        abort(403, 'ACCESO DENEGADO: Se requieren permisos de Super Administrador.');
    }
}
