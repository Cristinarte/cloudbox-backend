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
        // Crear la tabla de usuarios
        Schema::create('users', function (Blueprint $table) {
            $table->id();  // ID único del usuario
            $table->string('nombre');  // Nombre del usuario (Obligatorio)
            $table->string('alias');   // Alias del usuario (Obligatorio)
            $table->string('email')->unique();  // Email único (Obligatorio)
            $table->string('password');  // Contraseña del usuario (Obligatorio)
            $table->foreignId('role_id')->constrained('roles')->onDelete('restrict');  // Relación con la tabla 'roles', obligatorio
            $table->rememberToken();  // Token para "recordarme"
            $table->timestamps();  // Tiempos de creación y actualización
        });

        // Crear la tabla de tokens de restablecimiento de contraseña
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Crear la tabla de sesiones
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar las tablas en el reverso de la migración
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
