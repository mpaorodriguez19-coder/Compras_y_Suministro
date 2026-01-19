<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'orden_id',
        'cantidad',
        'descripcion',
        'unidad',
        'precio_unitario',
        'descuento',
        'valor'
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id', 'id');
    }
}
