<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenItemsTable extends Migration
{
    public function up()
    {
        Schema::create('orden_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes')->cascadeOnDelete();
            $table->text('descripcion');
            $table->string('unidad')->nullable();
            $table->decimal('cantidad', 12, 2)->default(0);
            $table->decimal('precio_unitario', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('valor', 12, 2)->default(0); // cantidad * precio_unitario - descuento
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orden_items');
    }
}

