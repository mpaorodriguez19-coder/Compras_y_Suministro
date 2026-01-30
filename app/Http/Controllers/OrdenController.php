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

        return view('ordenesindex', compact('ordenes'));
    }

    // FORMULARIO REPONER NUEVA ORDEN
    public function reponer()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();

        $ultimo = Orden::latest('id')->first();
        $numero = $ultimo ? $ultimo->id + 1 : 1;
        $numero = str_pad($numero, 6, '0', STR_PAD_LEFT);

        return view('orden.reponer', compact('proveedores', 'numero'));
    }

    // GUARDAR ORDEN + DETALLES
    public function store(Request $request)
    {
        Log::info('OrdenController@store', ['payload' => $request->all()]);

        $request->validate([
            'fecha' => 'required|date',
            'proveedor' => 'required|string',
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

        DB::beginTransaction();

        try {
            $ultimo = Orden::latest('id')->first();
            $numero = $ultimo ? $ultimo->id + 1 : 1;
            $numero = str_pad($numero, 6, '0', STR_PAD_LEFT);

           // dd($request->all());
            // 1. GESTIONAR PROVEEDOR (BUSCAR O ACTUALIZAR)
            $proveedorNombre = trim($request->proveedor);
            
            // Datos adicionales del formulario (si vienen)
            $datosProveedor = [
                'direccion' => $request->lugar, // Se mantiene 'lugar' como dirección por defecto si no hay otra
            ];

            if($request->filled('proveedor_rtn')) $datosProveedor['rtn'] = $request->proveedor_rtn;
            if($request->filled('proveedor_telefono')) $datosProveedor['telefono'] = $request->proveedor_telefono;
            if($request->filled('proveedor_correo')) $datosProveedor['correo'] = $request->proveedor_correo;
            if($request->filled('proveedor_direccion')) $datosProveedor['direccion'] = $request->proveedor_direccion; // Sobrescribe 'lugar' si se especificó dirección

            $proveedorObj = Proveedor::updateOrCreate(
                ['nombre' => $proveedorNombre],
                $datosProveedor
            );

            // 2. GESTIONAR SOLICITANTE (BUSCAR O CREAR)
            $solicitanteNombre = trim($request->solicitado);
            $solicitanteObj = User::firstOrCreate(
                ['name' => $solicitanteNombre],
                [
                    'email' => strtolower(str_replace(' ', '.', $solicitanteNombre)) . '@sistema.local',
                    'password' => bcrypt('12345678')
                ]
            );

            // Crear ORDEN
            $orden = Orden::create([
                'numero'         => $numero,
                'fecha'          => $request->fecha,
                'proveedor_id'   => $proveedorObj->id,
                'lugar'          => $request->lugar,
                'solicitante_id' => $solicitanteObj->id,
                'concepto'       => $request->concepto,
                'subtotal'       => 0,
                'descuento'      => 0,
                'impuesto'       => 0,
                'total'          => 0,
                'estado'         => 'pendiente',
            ]);

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
                throw new \Exception('Debe ingresar al menos un item válido.');
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

            DB::commit();

            // REDIRIGIR CON EL ID CORRECTO
            return redirect()->route('orden.espera', ['id' => $orden->id]);

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
    public function pdf($id)
    {
        Carbon::setLocale('es'); // Forzar español para las fechas
        $orden = Orden::with(['items', 'proveedor', 'solicitante'])
                      ->findOrFail($id);

        $pdf = Pdf::loadView('orden.espera_pdf', compact('orden'))
                  ->setPaper('letter', 'portrait');

        return $pdf->stream('orden_'.$orden->numero.'.pdf');
    }

    // BUSCAR PROVEEDORES (AJAX)
    public function buscarProveedores(Request $request) {
        $q = $request->get('q');
        $query = Proveedor::select('id', 'nombre', 'direccion');
        
        if (!empty($q)) {
            $query->where('nombre', 'like', "%{$q}%");
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
}
