<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Google_Client;

class GoogleLoginController extends Controller
{
    public function login(Request $request)
    {
        // Validar entrada
        $request->validate([
            'token' => 'required|string',
        ]);

        // Configurar cliente de Google
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID')); // Usa GOOGLE_CLIENT_ID
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));

        // Verificar el ID Token
        $token = $request->input('token');
        try {
            $payload = $client->verifyIdToken($token);
            if (!$payload) {
                return response()->json(['error' => 'Token inválido'], 400);
            }

            // Obtener datos del usuario
            $email = $payload['email'];
            $name = $payload['name'];
            $googleId = $payload['sub'];

            // Crear o actualizar usuario
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'nombre' => $name,
                    'alias' => $name,
                    'role_id' => 1,
                    'password' => bcrypt(uniqid()), // Contraseña aleatoria
                ]
            );

            // Generar token de Sanctum
            $token = $user->createToken('CloudBox')->plainTextToken;

            return response()->json([
                'token' => $token,
                'alias' => $user->alias,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error validando token de Google: ' . $e->getMessage());
            return response()->json(['error' => 'Error al validar el token: ' . $e->getMessage()], 400);
        }
    }
}