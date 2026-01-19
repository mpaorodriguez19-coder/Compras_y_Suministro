<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoSangre extends Model
{
    protected $table = 'tipos_sangre';

    protected $fillable = ['tipo'];

    /* Se puede compartir el mismo tipo de sangre */
    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }
}
