<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReposicionDetalle extends Model
{
    protected $table = 'reposicion_detalles';

    protected $fillable = [
        'reposicion_id',
        'orden_compra_detalle_id',
        'cantidad_usada',
        'observaciones'
    ];

    /* Se pueden tener varias reposiciones */
    public function reposicion()
    {
        return $this->belongsTo(Reposicion::class);
    }

    /* Se pueden tener varios detalles de ordenes */
    public function detalleOrden()
    {
        return $this->belongsTo(OrdenCompraDetalle::class, 'orden_compra_detalle_id');
    }
}
