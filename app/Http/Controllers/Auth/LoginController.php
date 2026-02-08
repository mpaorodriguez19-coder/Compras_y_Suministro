<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check() || Auth::guard('web')->check()) {
            return redirect()->route('dashboard');
        }

        // Verificar si existe al menos un admin
        $needsSetup = \App\Models\Admin::count() === 0;

        return view('auth.login', compact('needsSetup'));
    }

    public function setupFirstAdmin(Request $request)
    {
        // Solo permitir si no hay admins
        if (\App\Models\Admin::count() > 0) {
            return redirect()->route('login')->with('error', 'Ya existe un administrador.');
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:6',
        ]);

        // Crear Super Admin
        $admin = \App\Models\Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'super_admin',
        ]);

        // Generar archivo de credenciales
        $credentials = "CREDENCIALES DE SUPER ADMINISTRADOR\n";
        $credentials .= "====================================\n";
        $credentials .= "Fecha de creación: " . now() . "\n";
        $credentials .= "Nombre: " . $admin->name . "\n";
        $credentials .= "Correo: " . $admin->email . "\n";
        $credentials .= "Contraseña: " . $request->password . "\n";
        $credentials .= "====================================\n";
        $credentials .= "Por seguridad, elimine este archivo después de usarlo.\n";

        // Guardar en public/ o storage/ (usaremos public para fácil acceso dev, o storage download)
        // El usuario pidió "que genere un archivo", asumimos descarga directa o guardado
        
        $fileName = 'super_admin_credentials_' . date('Ymd_His') . '.txt';
        \Illuminate\Support\Facades\Storage::disk('local')->put($fileName, $credentials);
        
        // Loguear automáticamente
        Auth::guard('admin')->login($admin);

        return response()->streamDownload(function () use ($credentials) {
            echo $credentials;
        }, $fileName);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 1. Intentar como ADMIN
        if (Auth::guard('admin')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            \App\Services\BitacoraLogger::log('Inició sesión (Admin)', 'Auth');
            return redirect()->intended(route('dashboard'));
        }

        // 2. Intentar como USUARIO (Solicitante)
        if (Auth::guard('web')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            \App\Services\BitacoraLogger::log('Inició sesión (Usuario)', 'Auth');
            return redirect()->intended(route('dashboard')); // O ruta específica de usuario
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Log antes de hacer logout para tener el usuario activo
        \App\Services\BitacoraLogger::log('Cerró sesión', 'Auth');

        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
