<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Coleccion;
use App\Models\Contenido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; 

class ContenidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Contenido::whereHas('coleccion', function ($q) {
            $q->where('user_id', auth()->id()); // Filtra por el usuario autenticado
        });
    
        // Filtrar por coleccion_id si se proporciona en la request
        if ($request->has('coleccion_id')) {
            $query->where('coleccion_id', $request->coleccion_id);
        }
    
        return response()->json($query->get());
    }
    


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'coleccion_id' => 'required|exists:colecciones,id',
            'titulo' => 'required|string|max:255',
            'url' => 'required|url',
            'descripcion' => 'required|string',
            'imagen' => 'required|image|mimes:jpg,jpeg,png,gif|max:10240',
        ]);
    
        $coleccion = Coleccion::findOrFail($request->coleccion_id);
        if ($coleccion->user_id !== auth()->id()) {
            return response()->json(['error' => 'No tienes permiso para esta colección'], 403);
        }
    
        $imagen = null;
        if ($request->hasFile('imagen')) {
            $imagePath = $request->file('imagen')->store('images', 'public'); // Cambiado a 'images', 'public'
            $imagen = Storage::url($imagePath); // Esto dará /storage/images/nombre-archivo.png
            Log::info("Imagen guardada: " . $imagen); // Para depurar
        }
    
        $contenido = Contenido::create([
            'coleccion_id' => $request->coleccion_id,
            'titulo' => $request->titulo,
            'url' => $request->url,
            'descripcion' => $request->descripcion,
            'imagen' => $imagen,
        ]);
    
        return response()->json([
            'message' => 'Contenido creado exitosamente.',
            'data' => $contenido
        ], 201);
    }

    public function show(string $id)
    {
        try {
            // Intentamos obtener el contenido por su ID
            $contenido = Contenido::findOrFail($id);

            // Verificamos que el contenido pertenezca al usuario autenticado a través de su colección
            if ($contenido->coleccion->user_id !== auth()->id()) {
                return response()->json(['message' => 'No tienes permiso para ver este contenido.'], 403);
            }

            return response()->json(['data' => $contenido], 200);

        } catch (\Exception $e) {
            // Si ocurre un error, capturamos y lo mostramos
            return response()->json(['message' => 'Error al obtener el contenido.', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $contenido = Contenido::findOrFail($id);
    
        if ($contenido->coleccion->user_id !== auth()->id()) {
            return response()->json(['error' => 'No tienes permiso para actualizar este contenido'], 403);
        }
    
        // Validar: coleccion_id siempre requerido, los demás opcionales
        $request->validate([
            'coleccion_id' => 'required|exists:colecciones,id',
            'titulo' => 'sometimes|string|max:255',
            'url' => 'sometimes|url',
            'descripcion' => 'sometimes|string',
            'imagen' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
        ]);
    
        // Preparar los datos a actualizar (solo los campos enviados)
        $data = $request->only(['coleccion_id', 'titulo', 'url', 'descripcion']);
    
        // Manejar la imagen si se envía
        if ($request->hasFile('imagen')) {
            if ($contenido->imagen) {
                Storage::delete(str_replace('/storage', 'public', $contenido->imagen));
            }
            $imagePath = $request->file('imagen')->store('images', 'public');
            $data['imagen'] = Storage::url($imagePath);
            Log::info("Imagen actualizada: " . $data['imagen']);
        }
    
        // Actualizar solo los campos proporcionados
        $contenido->update($data);
    
        return response()->json([
            'message' => 'Contenido actualizado exitosamente',
            'data' => $contenido
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contenido = Contenido::findOrFail($id);

        // Verifica que el contenido pertenezca al usuario autenticado
        if ($contenido->coleccion->user_id !== auth()->id()) {
            return response()->json(['error' => 'No tienes permiso para eliminar este contenido'], 403);
        }

        // Eliminar la imagen si existe
        if ($contenido->imagen) {
            Storage::delete(str_replace('/storage', 'public', $contenido->imagen));
        }

        // Eliminar el contenido
        $contenido->delete();

        // Devolver respuesta JSON indicando éxito
        return response()->json(['message' => 'Contenido eliminado exitosamente'], 200);
    }
}