<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpleadoRRHH extends Model
{
    use HasFactory;

    protected $connection = 'recursos_humanos';
    protected $table = 'empleados';
    protected $primaryKey = 'DNI';
    public $incrementing = false;
    protected $keyType = 'string';

    // The table has timestamps based on migration
    public $timestamps = true;
}
