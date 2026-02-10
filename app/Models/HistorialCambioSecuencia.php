<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialCambioSecuencia extends Model
{
    use HasFactory;

    protected $table = 'historial_cambios_secuencia';

    protected $fillable = [
        'user_id',
        'user_name',
        'valor_anterior',
        'valor_nuevo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
