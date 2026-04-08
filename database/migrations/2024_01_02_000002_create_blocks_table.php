<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained('lots')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('client_name');
            $table->string('client_phone', 30);
            $table->dateTime('expires_at');
            $table->enum('status', ['active', 'cancelled', 'expired', 'converted'])->default('active');
            $table->foreignId('extended_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['lot_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
