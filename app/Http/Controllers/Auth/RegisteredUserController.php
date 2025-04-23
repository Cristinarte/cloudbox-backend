<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\Role; // Importa el modelo Role
use Illuminate\Http\JsonResponse;


class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
       
        // Validación del formulario

        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'alias' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required',  Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],  // Validamos que el role_id exista en la tabla roles
        ], [
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'required' => 'El campo :attribute es obligatorio.',
        ]
    
    
        );
    
        // Si el rol no se pasa (es decir, es un usuario común), asignamos el rol por defecto
        $roleId = $request->role_id ?? 1;  // Si no hay role_id en la solicitud, asigna 1 por defecto (usuario)
    
        // Si el usuario es un administrador, permite que se elija el rol
        if (Auth::check() && Auth::user()->role_id == 2) {
            // Si el que registra es un administrador, usa el role_id pasado en la solicitud
            $roleId = $request->role_id;
        }
    
        // Creación del usuario con el role_id seleccionado
        $user = User::create([
            'nombre' => $request->nombre,
            'alias' => $request->alias,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $roleId, // Asigna el rol de acuerdo a las reglas anteriores
        ]);
    
        
        // Generamos el evento de registro
        event(new Registered($user));
    
        // Iniciamos sesión con el nuevo usuario
        Auth::login($user);
    
        return response()->noContent();


    }
    
    
}