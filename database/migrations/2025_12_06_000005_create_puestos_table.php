<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('puestos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_puesto');
            $table->string('descripcion')->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('puestos');
    }
};
