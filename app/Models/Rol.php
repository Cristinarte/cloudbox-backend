<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    //
    use HasFactory;

    protected $fillable = ['rol'];  // Columna que va a almacenar el nombre del rol

    /**
     * Relación: Un rol puede tener muchos usuarios.
     */
    public function users()
    {
        return $this->hasMany(User::class);  // Relación de uno a muchos con la tabla users
    }
}
