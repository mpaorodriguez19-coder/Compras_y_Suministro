<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Orden;

class OrdenCompraDetalle extends Model
{
    // 🔹 Tabla correcta
    protected $table = 'orden_compra_detalles';

    // 🔹 Campos que se pueden asignar masivamente
    protected $fillable = [
        'orden_compra_id',   // FK a ordenes
        'cantidad',
        'descripcion',
        'unidad',
        'precio_unitario',
        'descuento',
        'valor',
    ];

    // 🔹 RELACIÓN CON ORDEN
    public function orden()
    {
        return $this->belongsTo(
            Orden::class,
            'orden_compra_id',  // FK en esta tabla
            'id'                // PK en la tabla ordenes
        );
    }
}
