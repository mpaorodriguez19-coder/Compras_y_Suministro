<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenCompra;
use App\Models\OrdenItem;   // ✅ FALTABA

class OrdenCompraController extends Controller
{
    public function index()
    {
        return view('ordenesindex');
    }

    // ==========================
    // GUARDAR ORDEN
    // ==========================
    public function store(Request $request)
    {

        // ✅ VALIDACION BASICA
        $request->validate([
            'proveedor' => 'required',
            'fecha' => 'required',
            'total' => 'required',
            'descripcion' => 'required|array'
        ]);

        // 1️⃣ GUARDAR ORDEN PRINCIPAL
        $orden = OrdenCompra::create([
            'proveedor' => $request->proveedor,
            'rtn' => $request->rtn,
            'fecha' => $request->fecha,
            'total' => $request->total,
        ]);

        // 2️⃣ GUARDAR DETALLES
        foreach ($request->descripcion as $i => $desc) {

            OrdenItem::create([
                'orden_compra_id' => $orden->id,
                'descripcion' => $desc,
                'unidad' => $request->unidad[$i],
                'cantidad' => $request->cantidad[$i],
                'precio' => $request->precio[$i],
                'valor' => $request->valor[$i],
            ]);
        }

        // 3️⃣ REDIRECCIONAR A IMPRESION
        return redirect()->route('orden.espera', $orden->id);
    }


    // ==========================
    // MOSTRAR ORDEN
    // ==========================
    public function espera($id)
    {
        $orden = OrdenCompra::with('items')->findOrFail($id);

        return view('espera', compact('orden'));
    }

    public function guardar(Request $request)
{
    // Crear la orden principal
    $orden = new OrdenCompra();
    $orden->numero = $this->generarNumero(); // función para generar un número de orden
    $orden->fecha = $request->fecha;
    $orden->proveedor = $request->proveedor;
    $orden->lugar = $request->lugar;
    $orden->solicitado_por = $request->solicitado;
    $orden->concepto = $request->concepto;
    $orden->sub_total = $request->sub_total;
    $orden->descuento_total = $request->descuento_total;
    $orden->impuesto = $request->impuesto;
    $orden->total = $request->total;
    $orden->save();

    // Guardar los items de la orden
    $cantidades = $request->cantidad;
    $descripciones = $request->descripcion;
    $unidades = $request->unidad;
    $precios = $request->precio_unitario;
    $descuentos = $request->descuento;
    $valores = $request->valor;

    foreach($cantidades as $i => $cantidad){
        OrdenItem::create([
            'orden_id' => $orden->id,
            'cantidad' => $cantidad,
            'descripcion' => $descripciones[$i],
            'unidad' => $unidades[$i],
            'precio_unitario' => $precios[$i],
            'descuento' => $descuentos[$i],
            'valor' => $valores[$i]
        ]);
    }

    // Redirigir a la página de impresión
    return redirect()->route('orden.imprimir', $orden->id);
}

}
