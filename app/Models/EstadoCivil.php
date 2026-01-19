<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoCivil extends Model
{
    protected $table = 'estados_civiles';

    protected $fillable = ['nombre'];

    /* Un estado civil, puede ser usado por varios empleados*/
    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }
}
