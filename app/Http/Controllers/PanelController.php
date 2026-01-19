<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\Orden;

class PanelController extends Controller
{
     public function informeDetallado(Request $request) {
        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());

        $ordenes = Orden::with(['proveedor', 'items'])
                        ->whereDate('fecha', '>=', $desde)
                        ->whereDate('fecha', '<=', $hasta)
                        ->orderBy('fecha', 'desc')
                        ->get();

        return view('panel.informe-detallado', compact('ordenes', 'desde', 'hasta'));
    }

    public function comprasProveedor(Request $request) {
        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());

        // Proveedores con sus órdenes filtradas
        $proveedores = Proveedor::whereHas('ordenes', function($q) use ($desde, $hasta){
                            $q->whereDate('fecha', '>=', $desde)
                              ->whereDate('fecha', '<=', $hasta);
                        })
                        ->with(['ordenes' => function($q) use ($desde, $hasta){
                            $q->whereDate('fecha', '>=', $desde)
                              ->whereDate('fecha', '<=', $hasta)
                              ->orderBy('fecha', 'desc');
                        }])
                        ->get();

        return view('panel.compras-proveedor', compact('proveedores', 'desde', 'hasta'));
    }

    public function resumenProveedor(Request $request) {
        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());

        // Proveedores con suma de totales filtrada
        $proveedores = Proveedor::whereHas('ordenes', function($q) use ($desde, $hasta){
                            $q->whereDate('fecha', '>=', $desde)
                              ->whereDate('fecha', '<=', $hasta);
                        })
                        ->withSum(['ordenes' => function($q) use ($desde, $hasta){
                            $q->whereDate('fecha', '>=', $desde)
                              ->whereDate('fecha', '<=', $hasta);
                        }], 'total')
                        ->get();

        return view('panel.resumen-proveedor', compact('proveedores', 'desde', 'hasta'));
    }

    public function informe(Request $request) {
        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());

        $ordenes = Orden::with('solicitante')
                        ->whereDate('fecha', '>=', $desde)
                        ->whereDate('fecha', '<=', $hasta)
                        ->orderBy('fecha', 'desc')
                        ->get();

        return view('panel.informe', compact('ordenes', 'desde', 'hasta'));
    }

    public function transparencia() {
        return view('panel.transparencia');
    }
}
