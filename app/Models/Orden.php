<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrdenItem;

class Orden extends Model
{
    use HasFactory;

    // 🔹 Especificar la tabla correcta
    protected $table = 'ordenes';

    // 🔹 Campos que se pueden asignar masivamente
    protected $fillable = [
        'numero',
        'fecha',
        'proveedor_id',
        'lugar',
        'solicitante_id',
        'concepto',
        'subtotal',
        'descuento',
        'impuesto',
        'total',
        'estado'
    ];

    // 🔹 RELACIÓN CON LOS ITEMS (OrdenItem)
    public function items()
    {
        return $this->hasMany(OrdenItem::class, 'orden_id', 'id');
    }

    // 🔹 RELACIÓN CON PROVEEDOR
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'id');
    }

    // 🔹 RELACIÓN CON SOLICITANTE
    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_id', 'id');
    }
}
