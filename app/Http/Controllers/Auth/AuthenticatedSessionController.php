<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Validar las credenciales
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Crear un token de Sanctum
            $token = $user->createToken('YourAppName')->plainTextToken;

            return response()->json([
                'token' => $token,
                'alias' => $user->alias,
            ]);
        }

        return response()->json(['error' => 'Credenciales incorrectas'], 401);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        // Elimina el token actual del usuario
        $request->user()->currentAccessToken()->delete();

        // Devuelve una respuesta exitosa sin contenido
        return response()->noContent();
        }
}