<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contenidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coleccion_id')->constrained()->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('url');
            $table->string('imagen')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contenidos');
    }
};