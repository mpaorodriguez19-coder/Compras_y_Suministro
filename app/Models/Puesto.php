<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{
    protected $fillable = [
        'nombre_puesto',
        'descripcion'
    ];

    /* Un puesto puede tener varios empleados */
    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }
}
