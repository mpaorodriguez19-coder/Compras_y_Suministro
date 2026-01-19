<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nacionalidad extends Model
{
    protected $fillable = ['nombre'];

    /* Una nacionalidad puede tener varios empleados */
    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }
}
