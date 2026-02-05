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

        $query = Orden::with(['proveedor', 'items'])
                        ->whereDate('fecha', '>=', $desde)
                        ->whereDate('fecha', '<=', $hasta)
                        ->orderBy('fecha', 'desc');

        if ($request->has('pdf')) {
            $ordenes = $query->get();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('panel.informe-detallado-pdf', compact('ordenes', 'desde', 'hasta'))
                  ->setPaper('letter', 'landscape');
            return $pdf->stream('informe_detallado.pdf');
        }

        $ordenes = $query->paginate(20)->appends($request->all());
        return view('panel.informe-detallado', compact('ordenes', 'desde', 'hasta'));
    }

    public function comprasProveedor(Request $request) {
        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());

        $query = Proveedor::whereHas('ordenes', function($q) use ($desde, $hasta){
                            $q->whereDate('fecha', '>=', $desde)
                              ->whereDate('fecha', '<=', $hasta);
                        })
                        ->with(['ordenes' => function($q) use ($desde, $hasta){
                            $q->whereDate('fecha', '>=', $desde)
                              ->whereDate('fecha', '<=', $hasta)
                              ->orderBy('fecha', 'desc');
                        }]);

        if ($request->has('pdf')) {
            $proveedores = $query->get();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('panel.compras-proveedor-pdf', compact('proveedores', 'desde', 'hasta'))
                  ->setPaper('letter', 'portrait');
            return $pdf->stream('compras_proveedor.pdf');
        }

        $proveedores = $query->paginate(10)->appends($request->all());
        return view('panel.compras-proveedor', compact('proveedores', 'desde', 'hasta'));
    }

    public function resumenProveedor(Request $request) {
        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());

        $query = Proveedor::whereHas('ordenes', function($q) use ($desde, $hasta){
                            $q->whereDate('fecha', '>=', $desde)
                              ->whereDate('fecha', '<=', $hasta);
                        })
                        ->withSum(['ordenes' => function($q) use ($desde, $hasta){
                            $q->whereDate('fecha', '>=', $desde)
                              ->whereDate('fecha', '<=', $hasta);
                        }], 'total');
        
        if ($request->has('pdf')) {
            $proveedores = $query->get();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('panel.resumen-proveedor-pdf', compact('proveedores', 'desde', 'hasta'))
                  ->setPaper('letter', 'portrait');
            return $pdf->stream('resumen_proveedor.pdf');
        }

        $proveedores = $query->paginate(20)->appends($request->all());
        return view('panel.resumen-proveedor', compact('proveedores', 'desde', 'hasta'));
    }

    public function informe(Request $request) {
        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());

        $query = Orden::with('solicitante')
                        ->whereDate('fecha', '>=', $desde)
                        ->whereDate('fecha', '<=', $hasta)
                        ->orderBy('fecha', 'desc');

        if ($request->has('pdf')) {
            $ordenes = $query->get();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('panel.informe-pdf', compact('ordenes', 'desde', 'hasta'))
                  ->setPaper('letter', 'portrait');
            return $pdf->stream('informe_general.pdf');
        }

        $ordenes = $query->paginate(20)->appends($request->all());
        return view('panel.informe', compact('ordenes', 'desde', 'hasta'));
    }

    public function transparencia(Request $request) {
        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());

        $query = Orden::with(['proveedor', 'items'])
                        ->whereDate('fecha', '>=', $desde)
                        ->whereDate('fecha', '<=', $hasta)
                        ->orderBy('fecha', 'desc');

        if ($request->has('pdf')) {
            $ordenes = $query->get();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('panel.transparencia_pdf', compact('ordenes', 'desde', 'hasta'))
                ->setPaper('letter', 'landscape');
            return $pdf->stream('transparencia.pdf');
        }

        $ordenes = $query->paginate(20)->appends($request->all());
        return view('panel.transparencia', compact('ordenes', 'desde', 'hasta'));
    }

    /* METODOS PDF EXTRA PARA LOS DEMAS REPORTES */

    public function informeDetalladoPdf(Request $request) {
        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());

        $ordenes = Orden::with(['proveedor', 'items'])
                        ->whereDate('fecha', '>=', $desde)
                        ->whereDate('fecha', '<=', $hasta)
                        ->orderBy('fecha', 'desc')
                        ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('panel.informe-detallado-pdf', compact('ordenes', 'desde', 'hasta'))
                  ->setPaper('letter', 'landscape'); // Tabla ancha
        return $pdf->stream('informe_detallado.pdf');
    }

    public function comprasProveedorPdf(Request $request) {
        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());

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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('panel.compras-proveedor-pdf', compact('proveedores', 'desde', 'hasta'))
                  ->setPaper('letter', 'portrait');
        return $pdf->stream('compras_proveedor.pdf');
    }

    public function resumenProveedorPdf(Request $request) {
        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());

        $proveedores = Proveedor::whereHas('ordenes', function($q) use ($desde, $hasta){
                            $q->whereDate('fecha', '>=', $desde)
                              ->whereDate('fecha', '<=', $hasta);
                        })
                        ->withSum(['ordenes' => function($q) use ($desde, $hasta){
                            $q->whereDate('fecha', '>=', $desde)
                              ->whereDate('fecha', '<=', $hasta);
                        }], 'total')
                        ->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('panel.resumen-proveedor-pdf', compact('proveedores', 'desde', 'hasta'))
                  ->setPaper('letter', 'portrait');
        return $pdf->stream('resumen_proveedor.pdf');
    }

    public function informePdf(Request $request) {
        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());

        $ordenes = Orden::with('solicitante')
                        ->whereDate('fecha', '>=', $desde)
                        ->whereDate('fecha', '<=', $hasta)
                        ->orderBy('fecha', 'desc')
                        ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('panel.informe-pdf', compact('ordenes', 'desde', 'hasta'))
                  ->setPaper('letter', 'portrait');
        return $pdf->stream('informe_general.pdf');
    }
}
