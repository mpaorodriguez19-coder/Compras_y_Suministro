<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reposicion_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reposicion_id')->constrained('reposiciones')->cascadeOnDelete();
            $table->foreignId('orden_compra_detalle_id')->constrained('orden_compra_detalles');
            $table->decimal('cantidad_usada', 10, 2);
            $table->string('observaciones')->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('reposicion_detalles');
    }
};
