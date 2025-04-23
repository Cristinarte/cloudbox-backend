<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Compartido extends Model
{
    use HasFactory;

    protected $table = 'compartidos';

    protected $fillable = [
        'user_id',
        'coleccion_id',
        'contenido_id',
        'token',
    ];

    // Quien comparte
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // La colección que se comparte (si aplica)
    public function coleccion()
    {
        return $this->belongsTo(Coleccion::class);
    }

    // El contenido que se comparte (si aplica)
    public function contenido()
    {
        return $this->belongsTo(Contenido::class);
    }
}