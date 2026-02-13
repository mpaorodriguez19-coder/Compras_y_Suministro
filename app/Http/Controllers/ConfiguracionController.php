<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuracion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $configs = Configuracion::all()->keyBy('key');
        
        // Obtener siguiente número de OC desde la configuración
        $nextIdConfig = Configuracion::where('key', 'next_oc_id')->first();
        $nextId = $nextIdConfig ? $nextIdConfig->value : 1;

        // Obtener historial de cambos
        $historial = \App\Models\HistorialCambioSecuencia::with('user')
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return view('configuracion.index', compact('configs', 'nextId', 'historial'));
    }

    public function update(Request $request)
    {
        Log::info('ConfiguracionController@update', ['next_oc_id' => $request->next_oc_id, 'all' => $request->all()]);

        $request->validate([
            'firma_oc_nombre_1' => 'nullable|string|max:255',
            'firma_oc_puesto_1' => 'nullable|string|max:255',
            'firma_oc_nombre_2' => 'nullable|string|max:255',
            'firma_oc_puesto_2' => 'nullable|string|max:255',
            'firma_oc_nombre_3' => 'nullable|string|max:255',
            'firma_oc_puesto_3' => 'nullable|string|max:255',
            'next_oc_id'        => 'nullable|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Guardar Firmas
            foreach ($request->except(['_token', 'next_oc_id', '_method']) as $key => $value) {
                Configuracion::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error guardando configuraciones: " . $e->getMessage());
            return back()->with('error', 'Error al guardar configuración.');
        }

        // Guardar/Actualizar Secuencia de Orden de Compra en la tabla de configuraciones
        if ($request->has('next_oc_id') && $request->next_oc_id) {
            $newVal = (int) $request->next_oc_id;
            
            // Obtener valor anterior
            $config = Configuracion::where('key', 'next_oc_id')->first();
            $oldVal = $config ? (int) $config->value : 0;

            if ($newVal != $oldVal) {
                // Actualizar configuración
                Configuracion::updateOrCreate(
                    ['key' => 'next_oc_id'],
                    ['value' => $newVal]
                );

                // Obtener nombre de usuario (Admin o User) y ID
                $userName = 'Sistema';
                $userId = null;

                if (Auth::guard('admin')->check()) {
                    $userName = Auth::guard('admin')->user()->name . ' (Admin)';
                } elseif (Auth::check()) {
                    $userName = Auth::user()->name;
                    $userId = Auth::id();
                }

                // Registrar en historial
                \App\Models\HistorialCambioSecuencia::create([
                    'user_id'       => $userId,
                    'user_name'     => $userName,
                    'valor_anterior'=> $oldVal,
                    'valor_nuevo'   => $newVal,
                ]);
            }
        }

        return redirect()->route('configuracion.index')->with('success', 'Configuración actualizada exitosamente.');
    }
}
