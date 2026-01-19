<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiario extends Model
{
    protected $fillable = [
        'empleado_id',
        'nombre',
        'porcentaje',
        'parentezco',
        'dni'
    ];

    /* Un beneficiario puede pertenecer a un empleado */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
