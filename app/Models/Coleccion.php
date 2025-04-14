<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coleccion extends Model
{
    use HasFactory;

    // Especificar el nombre de la tabla en español
    protected $table = 'colecciones';

    protected $fillable = ['user_id', 'nombre', 'imagen'];

    // Relación muchos a uno con users
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación uno a muchos con contenidos
    public function contenidos()
    {
        return $this->hasMany(Contenido::class);
    }
}