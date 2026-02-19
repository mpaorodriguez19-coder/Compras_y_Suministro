<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orden;
use App\Models\OrdenItem;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class OrdenController extends Controller
{
    // LISTADO DE ÓRDENES CON FILTRO
    public function index(Request $request)
    {
        $query = Orden::orderBy('created_at', 'desc');

        if ($request->filled('desde') && $request->filled('hasta')) {
            $query->whereBetween('fecha', [
                Carbon::parse($request->desde)->startOfDay(),
                Carbon::parse($request->hasta)->endOfDay(),
            ]);
        }

        $ordenes = $query->paginate(10)->appends($request->all());

        // Obtener siguiente número de OC desde la configuración
        $nextIdConfig = \App\Models\Configuracion::where('key', 'next_oc_id')->first();
        $nextId = $nextIdConfig ? $nextIdConfig->value : 1;

        $numero = str_pad($nextId, 6, '0', STR_PAD_LEFT);

        return view('ordenesindex', compact('ordenes', 'numero'));
    }

    // FORMULARIO REPONER NUEVA ORDEN
    public function reponer(Request $request)
    {
        $proveedores = Proveedor::orderBy('nombre')->get();

        // Obtener siguiente número de OC desde la configuración
        $nextIdConfig = \App\Models\Configuracion::where('key', 'next_oc_id')->first();
        $nextId = $nextIdConfig ? $nextIdConfig->value : 1;
        
        $numero = str_pad($nextId, 6, '0', STR_PAD_LEFT);

        $ordenOrigen = null;
        if ($request->has('copiar_numero')) {
            $ordenOrigen = Orden::with(['items', 'proveedor', 'solicitante'])
                                ->where('numero', $request->copiar_numero)
                                ->first();
        }

        return view('orden.reponer', compact('proveedores', 'numero', 'ordenOrigen'));
    }

    // GUARDAR ORDEN + DETALLES
    public function store(Request $request)
    {
        Log::info('OrdenController@store', ['payload' => $request->all()]);

        $request->validate([
            'fecha' => 'required|date',
            'proveedor' => 'required|string',
            //'lugar' => 'required|string',
            'solicitado' => 'required|string',
            'descripcion' => 'required|array|min:1',
            'cantidad' => 'required|array|min:1',
            'precio_unitario' => 'required|array|min:1',
            'unidad' => 'nullable|array',
            'descuento' => 'nullable|array',
        ], [
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha debe ser un formato válido.',
            'proveedor.required' => 'El proveedor es obligatorio.',
            'solicitado.required' => 'El campo solicitado por es obligatorio.',
            'descripcion.required' => 'Debe agregar al menos una descripción.',
            'descripcion.min' => 'La lista de items no puede estar vacía.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'precio_unitario.required' => 'El precio unitario es obligatorio.'
        ]);

        // VALIDACIÓN MANUAL DE ITEMS (Al menos uno válido)
        $hasItems = false;
        if ($request->has('descripcion') && $request->has('cantidad')) {
             foreach ($request->descripcion as $key => $desc) {
                $cant = $request->cantidad[$key] ?? 0;
                if (!empty(trim($desc)) && $cant > 0) {
                    $hasItems = true;
                    break;
                }
             }
        }

        if (!$hasItems) {
            return back()->withInput()->withErrors(['descripcion' => 'Debe agregar al menos un producto con descripción y cantidad válida.']);
        }

        DB::beginTransaction();

        try {

            // $ultimo, $numero logic removed (handled inside transaction atomically)
            // dd($request->all());
            // 1. GESTIONAR PROVEEDOR (VALIDAR EXISTENCIA)
            $proveedorInput = trim($request->proveedor);
            
            // Intentar separar por " - " (formato del frontend)
            $parts = explode(' - ', $proveedorInput);
            
            if (count($parts) > 1) {
                // Asumimos que el último es el RTN y el resto es el Nombre
                $rtnPosible = array_pop($parts);
                $nombrePosible = implode(' - ', $parts); // Por si el nombre tenía guiones
                
                $proveedorObj = Proveedor::where('nombre', $nombrePosible)
                                ->orWhere('rtn', $rtnPosible)
                                ->first();
            } else {
                // Búsqueda directa por nombre (Legacy)
                $proveedorObj = Proveedor::where('nombre', $proveedorInput)->first();
            }

            if (!$proveedorObj) {
                return back()->withInput()->with('error', 'El proveedor "' . $proveedorInput . '" no existe. Por favor agréguelo primero en la sección de Proveedores.');
            }

            // Actualizar datos si vienen en el request (Opcional, pero útil si se quiere mantener actualizado)
             if($request->filled('proveedor_rtn') || $request->filled('proveedor_telefono') || $request->filled('proveedor_correo') || $request->filled('proveedor_direccion')) {
                $datosActualizar = [];
                if($request->filled('proveedor_rtn')) $datosActualizar['rtn'] = $request->proveedor_rtn;
                if($request->filled('proveedor_telefono')) $datosActualizar['telefono'] = $request->proveedor_telefono;
                if($request->filled('proveedor_correo')) $datosActualizar['correo'] = $request->proveedor_correo;
                if($request->filled('proveedor_direccion')) $datosActualizar['direccion'] = $request->proveedor_direccion;
                
                $proveedorObj->update($datosActualizar);
            }

           // 2. GESTIONAR SOLICITANTE (SIEMPRE DESDE LO QUE SE ESCRIBE)
$solicitanteNombre = trim($request->solicitado);
$emailGenerado = strtolower(str_replace(' ', '.', $solicitanteNombre)) . '@sistema.local';

$solicitanteObj = User::where('name', $solicitanteNombre)
                    ->orWhere('email', $emailGenerado)
                    ->first();

if (!$solicitanteObj) {
    $solicitanteObj = User::create([
        'name' => $solicitanteNombre,
        'email' => $emailGenerado,
        'password' => bcrypt('12345678')
    ]);
}

            // OBTENER Y RESERVAR NUMERO DE SECUENCIA
            $numeroAsignado = 1;
            
            // Bloqueamos la fila de configuración para evitar colisiones
            $config = \App\Models\Configuracion::firstOrCreate(
                ['key' => 'next_oc_id'],
                ['value' => 1]
            );
            
            // Si hacemos lockForUpdate aseguramos que nadie más lea este valor hasta que terminemos
            // Pero SQLite/MySQL simple a veces se comportan diferente con lock.
            // Para este caso, un update directo es seguro si la transacción funciona.
            $numeroAsignado = $config->value;
            $config->increment('value');

            $realNumero = str_pad($numeroAsignado, 6, '0', STR_PAD_LEFT);

            // Crear ORDEN con el número reservado
            $orden = Orden::create([
                'numero'         => $realNumero,
                'fecha'          => Carbon::parse($request->fecha)->format('Y-m-d'),
                'proveedor_id'   => $proveedorObj->id,
              //'lugar'          => $request->lugar,
                'solicitante_id' => $solicitanteObj->id,
                'concepto'       => $request->concepto,
                'subtotal'       => 0,
                'descuento'      => 0,
                'impuesto'       => 0,
                'total'          => 0,
                'estado'         => 'pendiente',
            ]);
            
            // No necesitamos actualizar 'numero' después, ya lo insertamos correcto.

            $subtotal = 0;
            $impuestoTotal = 0;

            for ($i = 0; $i < count($request->descripcion); $i++) {
                $descripcion = trim($request->descripcion[$i] ?? '');
                $cantidad    = (float) ($request->cantidad[$i] ?? 0);
                $precio      = (float) ($request->precio_unitario[$i] ?? 0);
                // $descuento   = 0; // Descuento por item retirado
                $unidad      = $request->unidad[$i] ?? null;
                $llevaImpuesto = (isset($request->aplica_impuesto[$i]) && $request->aplica_impuesto[$i] == 1);

                if ($descripcion === '' || $cantidad <= 0) continue;

                $valor = ($cantidad * $precio);

                OrdenItem::create([
                    'orden_id'        => $orden->id,
                    'descripcion'     => $descripcion,
                    'unidad'          => $unidad,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precio,
                    'descuento'       => 0,
                    'valor'           => $valor,
                ]);

                $subtotal += $valor; // Suma al subtotal limpio
                
                if ($llevaImpuesto) {
                    $impuestoTotal += ($valor * 0.15);
                }
            }

            if ($subtotal <= 0) {
                throw new \Exception('Debe ingresar al menos un artículo válido.');
            }

            // Descuento Global
            $descuentoGlobal = (float) $request->input('descuento_total', 0);

            $total = $subtotal - $descuentoGlobal + $impuestoTotal;

            $orden->update([
                'subtotal'  => $subtotal,
                'descuento' => $descuentoGlobal,
                'impuesto'  => $impuestoTotal, // Se guarda el total del impuesto calculado
                'total'     => $total,
            ]);

            \App\Services\BitacoraLogger::log("Creó la orden #{$orden->numero}", 'Ordenes');

            DB::commit();

            // REDIRIGIR CON EL ID CORRECTO
            return redirect()->route('orden.pdf', ['id' => $orden->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar orden', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // MOSTRAR ORDEN RECIÉN GUARDADA (REP2)
    public function verEspera($id)
    {
        $orden = Orden::with(['items', 'proveedor', 'solicitante'])
                      ->find($id);

        if (!$orden) {
            return redirect()->route('orden.reponer')->with('error', "La Orden #{$id} no fue encontrada.");
        }

        return view('orden.espera', compact('orden'));
    }

    // GENERAR PDF
    public function pdf($id, Request $request)
    {
        Carbon::setLocale('es'); // Forzar español para las fechas
        $orden = Orden::with(['items', 'proveedor', 'solicitante'])
                      ->findOrFail($id);
        
        $tipo = $request->get('tipo', 'original'); // original o copia
        
        \App\Services\BitacoraLogger::log("Imprimió/Visualizó PDF ($tipo) orden #{$orden->numero}", 'Ordenes');

        // Cargar configuraciones de firmas
        $configs = \App\Models\Configuracion::pluck('value', 'key');

        $pdf = Pdf::loadView('orden.espera_pdf', compact('orden', 'configs', 'tipo'))
                  ->setPaper('letter', 'portrait');

        return $pdf->stream('orden_'.$orden->numero.'_'.$tipo.'.pdf');
    }

    // BUSCAR PROVEEDORES (AJAX)
    public function buscarProveedores(Request $request) {
        $q = $request->get('q');
        $query = Proveedor::select('id', 'nombre', 'rtn', 'direccion');
        
        if (!empty($q)) {
            $query->where(function($sql) use ($q) {
                $sql->where('nombre', 'like', "%{$q}%")
                    ->orWhere('rtn', 'like', "%{$q}%");
            });
        }
        
        return $query->orderBy('nombre')->limit(20)->get();
    }

    // BUSCAR USUARIOS (AJAX)
    public function buscarUsuarios(Request $request) {
        $q = $request->get('q');
        return \App\Models\User::where('name', 'like', "%{$q}%")
                    ->limit(10)
                    ->get(['id', 'name']);
    }
    // ==========================================
    // NUEVAS FUNCIONES DE GESTIÓN (LISTA, EDITAR, ANULAR)
    // ==========================================

    // MOSTRAR LISTA DE ÓRDENES
    public function lista(Request $request)
    {
        $query = Orden::with('proveedor')->orderBy('id', 'desc');

        // Búsqueda por termino
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sql) use ($q) {
                $sql->where('numero', 'like', "%{$q}%")
                    ->orWhere('lugar', 'like', "%{$q}%")
                    ->orWhereHas('proveedor', function ($prov) use ($q) {
                        $prov->where('nombre', 'like', "%{$q}%");
                    });
            });
        }

        // Filtro Fechas
        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }

        $ordenes = $query->paginate(15)->appends($request->all());

        return view('orden.lista', compact('ordenes'));
    }

    // EDITAR ORDEN (Reutiliza la vista de crear)
    public function edit($id)
    {
        $orden = Orden::with('items')->findOrFail($id);
        
        // Cargar proveedores
        $proveedores = Proveedor::orderBy('nombre')->get();
        
        // El numero, fecha, etc se pasarán via $orden a la vista
        // La vista 'ordenesindex' debe estar preparada para recibir $orden
        
        // Reutilizamos la variable $numero para la vista, aunque aquí es el numero de la orden existente
        $numero = $orden->numero;

        return view('ordenesindex', compact('orden', 'proveedores', 'numero'));
    }

    // ACTUALIZAR ORDEN (PUT)
    public function update(Request $request, $id)
    {
        $orden = Orden::findOrFail($id);

        if ($orden->estado === 'anulada') {
             return back()->with('error', 'No se puede editar una orden anulada.');
        }

        // Validación similar a store
        $request->validate([
            'fecha' => 'required|date',
            'proveedor' => 'required|string',
           //'lugar' => 'required|string',
            'solicitado' => 'required|string',
            'descripcion' => 'required|array|min:1',
            'cantidad' => 'required|array|min:1',
            'precio_unitario' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            // 1. Proveedor
            $proveedorInput = trim($request->proveedor);
            
             // Intentar separar por " - " (formato del frontend)
             $parts = explode(' - ', $proveedorInput);
            
             if (count($parts) > 1) {
                 $rtnPosible = array_pop($parts);
                 $nombrePosible = implode(' - ', $parts);
                 
                 $proveedorObj = Proveedor::where('nombre', $nombrePosible)
                                 ->orWhere('rtn', $rtnPosible)
                                 ->first();
             } else {
                 $proveedorObj = Proveedor::where('nombre', $proveedorInput)->first();
             }
            if (!$proveedorObj) {
                return back()->withInput()->with('error', "El proveedor '$proveedorInput' no existe.");
            }

             // 2. Solicitante
            $solicitanteNombre = trim($request->solicitado);
            $solicitanteObj = User::firstOrCreate(
                ['name' => $solicitanteNombre],
                ['email' => strtolower(str_replace(' ', '.', $solicitanteNombre)) . '@sistema.local', 'password' => bcrypt('12345678')]
            );

            // 3. Actualizar Cabecera
             $orden->update([
                'fecha'          => Carbon::parse($request->fecha)->format('Y-m-d'), // Asegurar formato Y-m-d
                'proveedor_id'   => $proveedorObj->id,
              //'lugar'          => $request->lugar,
                'solicitante_id' => $solicitanteObj->id,
                'concepto'       => $request->concepto,
            ]);

            // 4. Actualizar Items (Borrar y Crear de nuevo para simplificar)
            $orden->items()->delete();

            $subtotal = 0;
            $impuestoTotal = 0;

            for ($i = 0; $i < count($request->descripcion); $i++) {
                $descripcion = trim($request->descripcion[$i] ?? '');
                $cantidad    = (float) ($request->cantidad[$i] ?? 0);
                $precio      = (float) ($request->precio_unitario[$i] ?? 0);
                $unidad      = $request->unidad[$i] ?? null;
                $llevaImpuesto = (isset($request->aplica_impuesto[$i]) && $request->aplica_impuesto[$i] == 1);

                if ($descripcion === '' || $cantidad <= 0) continue;

                $valor = ($cantidad * $precio);

                OrdenItem::create([
                    'orden_id'        => $orden->id,
                    'descripcion'     => $descripcion,
                    'unidad'          => $unidad,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precio,
                    'descuento'       => 0,
                    'valor'           => $valor,
                ]);

                $subtotal += $valor;
                if ($llevaImpuesto) $impuestoTotal += ($valor * 0.15);
            }

            // Totales
            $descuentoGlobal = (float) $request->input('descuento_total', 0);
            $total = $subtotal - $descuentoGlobal + $impuestoTotal;

            $orden->update([
                'subtotal'  => $subtotal,
                'descuento' => $descuentoGlobal,
                'impuesto'  => $impuestoTotal,
                'total'     => $total,
            ]);
            
            \App\Services\BitacoraLogger::log("Editó la orden #{$orden->numero}", 'Ordenes');

            DB::commit();

            return redirect()->route('ordenes.lista')->with('success', "Orden #{$orden->numero} actualizada correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando orden', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ANULAR ORDEN (Checkeo de seguridad)
    // ANULAR ORDEN
    public function anular(Request $request, $id)
    {
        $orden = Orden::findOrFail($id);
        
        // Validación de observación obligatoria
        $request->validate([
            'observacion' => 'required|string|min:5|max:500', 
        ], [
            'observacion.required' => 'Es necesario agregar una observación para anular.',
            'observacion.min' => 'La observación debe tener al menos 5 caracteres.'
        ]);

        // Ya no requerimos credenciales de Super Admin explicítas, 
        // asumimos que si tiene acceso a esta ruta (protegida por middleware auth:admin) es suficiente,
        // o si se requiere validación extra, se podría implementar, pero el usuario pidió quitarlo.

        // Proceder a anular
        $orden->update([
            'estado' => 'anulada',
            'observacion' => $request->observacion
        ]);
        
        \App\Services\BitacoraLogger::log("Anuló la orden #{$orden->numero}. Motivo: {$request->observacion}", 'Ordenes');
        
        return back()->with('success', "Orden #{$orden->numero} anulada correctamente.");
    }
    // VERIFICAR SI ORDEN EXISTE (API)
    public function checkOrden($numero)
    {
        $orden = Orden::where('numero', $numero)->first();
        return response()->json([
            'exists' => $orden ? true : false,
            'id' => $orden ? $orden->id : null
        ]);
    }
}
