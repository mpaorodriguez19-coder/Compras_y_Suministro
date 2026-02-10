<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('q')) {
            $search = $request->q;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $usuarios = $query->orderBy('name')->paginate(10);

        return view('usuarios.index', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'dni' => 'required|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        User::create([
            'name' => $request->nombres . ' ' . $request->apellidos, // Mantener compatibilidad con 'name'
            'username' => $request->username,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'password' => $request->password,
            'dni' => $request->dni,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
        ]);

        return redirect()->back()->with('success', 'Usuario creado exitosamente');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'dni' => 'required|string|max:20',
            'telefono' => 'nullable|string|max:20',
        ]);


        $data = [
            'name' => $request->nombres . ' ' . $request->apellidos,
            'username' => $request->username,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'dni' => $request->dni,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
        ];

        // Solo actualizar contraseña si se envía
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Usuario actualizado exitosamente');
    }
    // Método para buscar en RRHH (API)
    public function buscarEmpleadoRRHH(Request $request)
    {
        $identidad = $request->get('identidad');

        if (!$identidad) {
            return response()->json(['success' => false, 'message' => 'Identidad requerida.']);
        }

        try {
            // Buscar con guiones y sin guiones para flexibilidad
            $dniLimpio = str_replace('-', '', $identidad);
            $dniGuiones = substr($dniLimpio, 0, 4) . '-' . substr($dniLimpio, 4, 4) . '-' . substr($dniLimpio, 8);

            $empleado = \App\Models\EmpleadoRRHH::where('DNI', $identidad)
                ->orWhere('DNI', $dniLimpio)
                ->orWhere('DNI', $dniGuiones)
                ->first();

            if ($empleado) {
                // Generar sugerencia de usuario: primera_letra_nombre + apellido
                $primerNombre = strtolower($empleado->primer_nombre ?? '');
                $primerApellido = strtolower($empleado->primer_apellido ?? '');
                $username = substr($primerNombre, 0, 1) . $primerApellido;

                return response()->json([
                    'success' => true,
                    'nombres' => trim(($empleado->primer_nombre ?? '') . ' ' . ($empleado->segundo_nombre ?? '')),
                    'apellidos' => trim(($empleado->primer_apellido ?? '') . ' ' . ($empleado->segundo_apellido ?? '')),
                    'username' => $username,
                    'dni' => $empleado->DNI,
                    'telefono' => $empleado->telefono_celular,
                    'direccion' => $empleado->direccion_domicilio,
                    'email_sugerido' => $username . '@sistema.local'
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'No encontrado en RRHH.']); 
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
        }
    }
}
