<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index()
    {
        // Solo lista si es super admin (doble seguridad, aunque el middleware ya lo haga)
        if (Auth::guard('admin')->user()->role !== 'super_admin') {
            abort(403);
        }

        $admins = Admin::orderBy('name')->paginate(10);
        return view('admins.index', compact('admins'));
    }

    public function store(Request $request)
    {
        if (Auth::guard('admin')->user()->role !== 'super_admin') {
            abort(403);
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6',
        ]);

        Admin::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'admin', // Por defecto crea admins normales
        ]);

        return redirect()->route('admins.index')->with('success', 'Administrador creado exitosamente.');
    }

    public function update(Request $request, $id)
    {
        // Verificar rol de Super Admin
        if (Auth::guard('admin')->user()->role !== 'super_admin') {
            abort(403);
        }

        $admin = Admin::findOrFail($id);

        // PROTECCIÓN: No permitir editar al Super Admin (mismo rol)
        if ($admin->role === 'super_admin') {
            return back()->with('error', 'No puedes editar al Super Administrador.');
        }

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('admins')->ignore($admin->id)],
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        // Solo actualizar contraseña si se envió algo
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('admins.index')->with('success', 'Administrador actualizado correctamente.');
    }
}
