<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        // SEGURIDAD: Solo Super Admin
        if (Auth::guard('admin')->user()->role !== 'super_admin') {
            abort(403, 'No autorizado.');
        }

        $query = Bitacora::query();

        // Filtros
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        if ($request->filled('user')) {
            $query->where('user_name', 'like', '%' . $request->user . '%');
        }

        $bitacoras = $query->orderBy('created_at', 'desc')->paginate(20);

        // Obtener lista de módulos únicos para el filtro
        $modulos = Bitacora::select('modulo')->distinct()->pluck('modulo');

        return view('panel.bitacora', compact('bitacoras', 'modulos'));
    }
}
