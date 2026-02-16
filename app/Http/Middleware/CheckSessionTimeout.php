<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckSessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Si el usuario no está logueado, continuar.
        if (!Auth::guard('admin')->check()) {
            return $next($request);
        }

        // Usar el tiempo de vida de la sesión configurado (en minutos) convertido a segundos
        // Por defecto en Laravel es 120 minutos (2 horas)
        $maxIdleTime = config('session.lifetime') * 60; 

        $lastActivity = Session::get('last_activity');

        if ($lastActivity && (time() - $lastActivity > $maxIdleTime)) {
            Auth::guard('admin')->logout();
            Session::invalidate();
            Session::regenerateToken();

            return redirect()->route('login')->with('error', 'Su sesión ha expirado por inactividad. Por favor ingrese nuevamente.');
        }

        // Actualizar tiempo de última actividad
        Session::put('last_activity', time());

        return $next($request);
    }
}
