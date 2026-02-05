<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_type', // 'App\Models\User' o 'App\Models\Admin'
        'user_name', // Guardamos el nombre al momento de la acción
        'accion',
        'modulo',
        'ip_address',
    ];

    // Opcional: Relación polimórfica si quisieras acceder al usuario actual
    // public function user() {
    //     return $this->morphTo();
    // }
}
