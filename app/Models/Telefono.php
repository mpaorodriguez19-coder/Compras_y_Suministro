<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Telefono extends Model
{
    protected $fillable = [
        'empleado_id',
        'tipo',
        'numero'
    ];

    /* Relación: el celular pertenece a un empleado */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
