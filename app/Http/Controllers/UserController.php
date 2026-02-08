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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'dni' => 'required|string|max:15',
            'telefono' => 'nullable|string|max:15',
            'direccion' => 'nullable|string|max:255',
        ]);

        User::create([
            'name' => $request->name,
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id, 
            'dni' => 'required|string|max:15',
            'telefono' => 'nullable|string|max:15',
        ]);

        $data = [
            'name' => $request->name,
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
                // Concatenar nombre completo
                $nombreCompleto = trim(
                    ($empleado->primer_nombre ?? '') . ' ' .
                    ($empleado->segundo_nombre ?? '') . ' ' .
                    ($empleado->primer_apellido ?? '') . ' ' .
                    ($empleado->segundo_apellido ?? '')
                );

                // Generar nombre de usuario aleatorio: nombre.apellido + 3 digitos
                $baseUser = strtolower(($empleado->primer_nombre ?? 'user') . '.' . ($empleado->primer_apellido ?? 'new'));
                $randomUser = $baseUser . rand(100, 999);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'name' => $nombreCompleto,
                        'username' => $randomUser,
                        // Generar email sugerido si no tiene (opcional, aqui solo devolvemos el dato crudo si existiera email en rrhh, pero la tabla no tiene email explícito, usaremos el nombre para sugerir)
                        'dni' => $empleado->DNI,
                        'telefono' => $empleado->telefono_celular,
                        'direccion' => $empleado->direccion_domicilio
                    ]
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'No encontrado en RRHH.']); 
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
        }
    }
}
