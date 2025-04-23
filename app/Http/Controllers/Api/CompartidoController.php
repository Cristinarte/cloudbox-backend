<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Coleccion;
use App\Models\Contenido;
use App\Models\Compartido;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class CompartidoController extends Controller
{
    /**
     * Compartir una colección o contenido
     */
    public function share(Request $request, $id): JsonResponse
    {
        $tipo = $request->input('tipo', 'coleccion');
        
        if ($tipo === 'coleccion') {
            $coleccion = Coleccion::findOrFail($id);
            if ($coleccion->user_id !== auth()->id()) {
                return response()->json(['message' => 'No tienes permiso para compartir esta colección'], 403);
            }
            
            $compartido = new Compartido();
            $compartido->user_id = auth()->id();
            $compartido->coleccion_id = $coleccion->id;
            $compartido->token = Str::random(32);
            $compartido->save();
            
            return response()->json([
                'message' => 'Colección compartida con éxito',
                'token' => $compartido->token,
                'url' => "https://cloudbox-frontend-23du.onrender.com/compartido/{$compartido->token}"
            ], 201);
            
        } elseif ($tipo === 'contenido') {
            $contenido = Contenido::findOrFail($id);
            $coleccion = $contenido->coleccion;
            if ($coleccion->user_id !== auth()->id()) {
                return response()->json(['message' => 'No tienes permiso para compartir este contenido'], 403);
            }
            
            $compartido = new Compartido();
            $compartido->user_id = auth()->id();
            $compartido->contenido_id = $contenido->id;
            $compartido->token = Str::random(32);
            $compartido->save();
            
            return response()->json([
                'message' => 'Contenido compartido con éxito',
                'token' => $compartido->token,
                'url' => "https://cloudbox-frontend-23du.onrender.com/compartido/{$compartido->token}"
            ], 201);
        }
        
        return response()->json(['message' => 'Tipo de elemento a compartir no válido'], 400);
    }
    
    /**
     * Mostrar un elemento compartido a través de su token
     */
    public function show($token): JsonResponse
    {
        $compartido = Compartido::where('token', $token)->firstOrFail();

        if ($compartido->coleccion_id) {
            $coleccion = $compartido->coleccion;
            $contenidos = $coleccion->contenidos;

            return response()->json([
                'tipo' => 'coleccion',
                'coleccion' => $coleccion,
                'contenidos' => $contenidos,
                'compartido_por' => $compartido->user->nombre
            ]);
        } elseif ($compartido->contenido_id) {
            $contenido = $compartido->contenido;

            return response()->json([
                'tipo' => 'contenido',
                'contenido' => $contenido,
                'coleccion' => $contenido->coleccion,
                'compartido_por' => $compartido->user->nombre
            ]);
        }

        return response()->json(['message' => 'Elemento compartido no encontrado'], 404);
    }
    
    /**
     * Revocar acceso a un elemento compartido
     */
    public function revoke($token): JsonResponse
    {
        $compartido = Compartido::where('token', $token)->firstOrFail();
        
        if ($compartido->user_id !== auth()->id()) {
            return response()->json(['message' => 'No tienes permiso para revocar este acceso'], 403);
        }
        
        $compartido->delete();
        
        return response()->json(['message' => 'Acceso compartido revocado con éxito']);
    }
}