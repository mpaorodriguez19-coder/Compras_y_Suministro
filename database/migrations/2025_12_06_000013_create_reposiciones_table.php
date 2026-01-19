<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reposiciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_compra_id')->constrained('ordenes_compra')->cascadeOnDelete();
            $table->string('utilizado_por');
            $table->string('solicitado_por');
            $table->string('hecho_por');
            $table->date('fecha');
        });
    }

    public function down(): void {
        Schema::dropIfExists('reposiciones');
    }
};
