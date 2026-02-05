<?php

namespace App\Services;

use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class BitacoraLogger
{
    public static function log($accion, $modulo)
    {
        $user = null;
        $userType = null;
        $userName = 'Sistema';

        // Detectar si es Admin
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $userType = get_class($user);
            $userName = $user->name . ' (Admin)';
        } 
        // Detectar si es Usuario Normal
        elseif (Auth::check()) {
            $user = Auth::user();
            $userType = get_class($user);
            $userName = $user->name;
        }

        Bitacora::create([
            'user_id'    => $user ? $user->id : null,
            'user_type'  => $userType,
            'user_name'  => $userName,
            'accion'     => $accion,
            'modulo'     => $modulo,
            'ip_address' => Request::ip(),
        ]);
    }
}
