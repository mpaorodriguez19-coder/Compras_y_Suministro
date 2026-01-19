<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelEducativo extends Model
{
    protected $table = 'niveles_educativos';

    protected $fillable = ['nivel'];

    /* un nivel educativo, puede pertenecer a varios empleados */
    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }
}
