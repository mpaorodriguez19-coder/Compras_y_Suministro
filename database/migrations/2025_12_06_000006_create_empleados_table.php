<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('empleados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('codigo');
            $table->string('primer_nombre');
            $table->string('segundo_nombre')->nullable();
            $table->string('primer_apellido');
            $table->string('segundo_apellido')->nullable();
            $table->date('fecha_nacimiento');
            $table->string('sexo');
            $table->string('dni')->unique();
            $table->string('rtn')->unique();
            $table->foreignId('estado_civil_id')->constrained('estados_civiles');
            $table->foreignId('nacionalidad_id')->constrained('nacionalidades');
            $table->foreignId('tipo_sangre_id')->constrained('tipos_sangre');
            $table->foreignId('nivel_educativo_id')->constrained('niveles_educativos');
            $table->foreignId('puesto_id')->constrained('puestos');
            $table->string('direccion_domicilio');
            $table->string('referencia_domicilio')->nullable();
            $table->date('fecha_nombramiento')->nullable();
            $table->string('tipo_contrato');
            $table->decimal('salario_inicial', 8, 2);
            $table->string('foto')->nullable();
            $table->string('firma')->nullable();
            $table->string('huella')->nullable();
            $table->string('usuario_crea')->nullable();
            $table->string('usuario_modifica')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('empleados');
    }
};
