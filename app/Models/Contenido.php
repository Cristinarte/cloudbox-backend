<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contenido extends Model
{
    use HasFactory;

    // Especificar el nombre de la tabla en español
    protected $table = 'contenidos';

    protected $fillable = [
        'coleccion_id',
        'titulo',
        'descripcion',
        'url',
        'imagen',
    ];

    // Relación muchos a uno con colecciones
    public function coleccion()
    {
        return $this->belongsTo(Coleccion::class);
    }
}