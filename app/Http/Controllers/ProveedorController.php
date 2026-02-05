<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedor::query();

        if ($request->has('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('rtn', 'like', "%{$search}%");
            });
        }

        $proveedores = $query->orderBy('nombre')->paginate(10);

        return view('proveedores.index', compact('proveedores'));
    }

    public function store(Request $request)
    {
        $messages = [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres.',
            'rtn.required' => 'El RTN es obligatorio.',
            'rtn.size' => 'El RTN debe tener exactamente 14 caracteres.',
            'rtn.unique' => 'El RTN ya está registrado.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo debe ser válido.',
            'direccion.required' => 'La dirección es obligatoria.',
        ];

        $request->validate([
            'nombre' => 'required|string|max:255',
            'rtn' => 'required|string|size:14|unique:proveedores,rtn',
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|max:255',
            'direccion' => 'required|string',
        ], $messages);

        Proveedor::create($request->all());

        \App\Services\BitacoraLogger::log("Creó el proveedor {$request->nombre}", 'Proveedores');

        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $messages = [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres.',
            'rtn.required' => 'El RTN es obligatorio.',
            'rtn.size' => 'El RTN debe tener exactamente 14 caracteres.',
            'rtn.unique' => 'El RTN ya está registrado.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo debe ser válido.',
            'direccion.required' => 'La dirección es obligatoria.',
        ];

        $request->validate([
            'nombre' => 'required|string|max:255',
            'rtn' => 'required|string|size:14|unique:proveedores,rtn,' . $id,
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|max:255',
            'direccion' => 'required|string',
        ], $messages);

        $proveedor->update($request->all());

        \App\Services\BitacoraLogger::log("Actualizó el proveedor {$proveedor->nombre}", 'Proveedores');

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado exitosamente.');
    }
}
