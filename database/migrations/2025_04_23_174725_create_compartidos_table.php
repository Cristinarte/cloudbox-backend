<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('compartidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // quién comparte
    
            // Solo uno de estos dos se usará en cada fila
            $table->foreignId('coleccion_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('contenido_id')->nullable()->constrained()->onDelete('cascade');
    
            $table->string('token')->unique(); // enlace único
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compartidos');
    }
};