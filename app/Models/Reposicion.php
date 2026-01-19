<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reposicion extends Model
{
    protected $fillable = [
        'orden_compra_id',
        'utilizado_por',
        'solicitado_por',
        'hecho_por',
        'fecha'
    ];

    /* La Repocisiónpuede pertenecer a una orden */
    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class);
    }

    /* La Repocisión puede tener varias filas de detalles */
    public function detalles()
    {
        return $this->hasMany(ReposicionDetalle::class);
    }
}
