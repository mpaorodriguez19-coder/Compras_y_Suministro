<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends Model
{
    protected $table = 'ordenes_compra';

    protected $fillable = [
        'fecha',
        'proveedor_id',
        'lugar',
        'solicitado_por',
        'concepto',
        'subtotal',
        'descuento_total',
        'impuesto',
        'total'
    ];

    /* Cada orden de compra pertenece a un proveedor */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    /* Cada orden puede tener lineas de detalles*/
    public function detalles()
    {
        return $this->hasMany(OrdenCompraDetalle::class);
    }

    /* una orden puede tener varias reposiciones */
    public function reposiciones()
    {
        return $this->hasMany(Reposicion::class);
    }
}
