<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ordenes_compra', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->unsignedBigInteger('proveedor_id');
            $table->string('lugar');
            $table->string('solicitado_por');
            $table->string('concepto');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento_total', 10, 2);
            $table->decimal('impuesto', 10, 2);
            $table->decimal('total', 10, 2);
            $table->foreign('proveedor_id')->references('id')->on('proveedores');
        });
    }

    public function down(): void {
        Schema::dropIfExists('ordenes_compra');
    }
};
