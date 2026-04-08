<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->string('manzana', 10);
            $table->string('nro_lote', 10);
            $table->decimal('superficie', 10, 2);
            $table->string('zona', 50)->nullable();
            $table->decimal('fot', 5, 2)->nullable();
            $table->decimal('fos', 5, 2)->nullable();
            $table->decimal('h_maxima', 5, 2)->nullable();
            $table->string('observaciones', 100)->nullable();
            $table->decimal('precio', 12, 2)->default(0);
            $table->enum('estado', [
                'disponible',
                'bloqueado',
                'reservado',
                'vendido',
                'no_disponible',
                'oculto'
            ])->default('oculto');
            $table->timestamps();

            $table->unique(['manzana', 'nro_lote']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
