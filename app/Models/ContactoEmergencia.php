<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactoEmergencia extends Model
{
    protected $table = 'contactos_emergencia';

    protected $fillable = [
        'empleado_id',
        'nombre',
        'telefono',
        'parentezco'
    ];

    /* Un contacto puede pertenecer a un solo empleado */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
