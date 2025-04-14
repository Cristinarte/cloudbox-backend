<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role; 
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomResetPassword;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',     // Cambié 'name' por 'nombre' según tu migración
        'alias',      // Agregué 'alias' como campo asignable
        'email',      // 'email' es asignable
        'password',   // 'password' es asignable
        'role_id',    // 'role_id' ahora es asignable
    ];

    /**
     * Los atributos que deberían estar ocultos para la serialización.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Obtener los atributos que deben ser casteados.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Definir la relación con el modelo Role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id'); // Relación con el modelo Role
    }

    // Relación uno a muchos con colecciones
    public function colecciones()
    {
        return $this->hasMany(Coleccion::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }
}

