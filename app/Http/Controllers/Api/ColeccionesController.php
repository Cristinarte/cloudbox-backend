<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Coleccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Str;

class ColeccionesController extends Controller
{
    public function index()
    {
        $colecciones = Coleccion::where('user_id', auth()->id())->get();
        return response()->json($colecciones);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:24',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
        ]);
    
        $imagen = null;
        if ($request->hasFile('imagen')) {
            try {
                $imagePath = $request->file('imagen')->store('images', 'public'); // Especificamos el disco 'public'
                $imagen = Storage::url($imagePath);
                if (!file_exists(storage_path('app/' . $imagePath))) {
                    \Log::warning('El archivo no existe en el sistema de archivos: ' . storage_path('app/' . $imagePath));
                } else {
                    \Log::info('Imagen guardada en: ' . storage_path('app/' . $imagePath) . ' con URL: ' . $imagen);
                }
            } catch (\Exception $e) {
                \Log::error('Error al guardar la imagen: ' . $e->getMessage());
            }
        } else {
            \Log::warning('No se recibió ninguna imagen en la solicitud.');
        }
    
        $coleccion = Coleccion::create([
            'user_id' => auth()->id(),
            'nombre' => $request->nombre,
            'imagen' => $imagen,
        ]);
    
        return response()->json(['message' => 'Colección creada exitosamente.', 'data' => $coleccion], 201);
    }

    public function show(string $id)
    {
        $coleccion = Coleccion::with('contenidos')->findOrFail($id);
        if ($coleccion->user_id !== auth()->id()) {
            return response()->json(['error' => 'No tienes permiso.'], 403);
        }
        return response()->json($coleccion);
    }

    public function update(Request $request, string $id)
    {
        
        $coleccion = Coleccion::findOrFail($id);
        if ($coleccion->user_id !== auth()->id()) {
            return response()->json(['error' => 'No tienes permiso.'], 403);
        }
    
        $request->validate([
            'nombre' => 'required|string|max:24',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
        ]);
    
        $imagen = $coleccion->imagen;
        if ($request->hasFile('imagen')) {
            if ($coleccion->imagen) {
                Storage::delete(str_replace('/storage', 'public', $coleccion->imagen));
            }
            $imagePath = $request->file('imagen')->store('public/images');
            $imagen = Storage::url($imagePath);
        }
    
        $coleccion->update(['nombre' => $request->nombre, 'imagen' => $imagen]);
        return response()->json(['message' => 'Colección actualizada exitosamente.', 'data' => $coleccion]);
    }

    public function destroy(string $id)
    {
        $coleccion = Coleccion::findOrFail($id);
        if ($coleccion->user_id !== auth()->id()) {
            return response()->json(['error' => 'No tienes permiso.'], 403);
        }
        if ($coleccion->imagen) {
            Storage::delete(str_replace('/storage', 'public', $coleccion->imagen));
        }
        $coleccion->delete();
        return response()->json(['message' => 'Colección eliminada exitosamente.']);
    }
}