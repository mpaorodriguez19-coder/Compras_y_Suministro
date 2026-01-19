<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    // Campos asignables
    protected $fillable = [
        'codigo','primer_nombre','segundo_nombre','primer_apellido','segundo_apellido',
        'fecha_nacimiento','sexo','dni','rtn','estado_civil_id','nacionalidad_id',
        'tipo_sangre_id','nivel_educativo_id','puesto_id','direccion_domicilio',
        'referencia_domicilio','fecha_nombramiento','tipo_contrato','salario_inicial',
        'foto','firma','huella','usuario_crea','usuario_modifica'
    ];

    /* Relación: un empleado puede tener varios telefonos */
    public function telefonos() {
        return $this->hasMany(Telefono::class);
    }

    /* Relación: un empleado puede tener varios contactos de emergencia */
    public function contactosEmergencia() {
        return $this->hasMany(ContactoEmergencia::class);
    }

    /* Relación: un empleado puede tener varios beneficiarios*/
    public function beneficiarios() {
        return $this->hasMany(Beneficiario::class);
    }

    public function estadoCivil() { return $this->belongsTo(EstadoCivil::class); }
    public function nacionalidad() { return $this->belongsTo(Nacionalidad::class); }
    public function tipoSangre() { return $this->belongsTo(TipoSangre::class); }
    public function nivelEducativo() { return $this->belongsTo(NivelEducativo::class); }
    public function puesto() { return $this->belongsTo(Puesto::class); }
}
