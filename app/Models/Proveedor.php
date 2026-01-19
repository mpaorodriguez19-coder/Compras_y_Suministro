<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores'; // ✅ SOLUCIÓN CLAVE

    protected $fillable = [
        'nombre',
        'rtn',
        'nit',
        'direccion',
        'telefono',
        'correo',
        'contacto'
    ];

    /* Se pueden realizar varias ordenes de compra a proveedores */
    public function ordenesCompra()
    {
        return $this->hasMany(OrdenCompra::class, 'proveedor_id');
    }
}
