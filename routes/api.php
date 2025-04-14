<?php


use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ColeccionesController;
use App\Http\Controllers\Api\ContenidoController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Api\GoogleLoginController;



Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas para registro y login:

// Registro de usuario
Route::post('/register', [RegisteredUserController::class, 'store']);

// Login de usuario
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Login con google
Route::post('/google-login', [GoogleLoginController::class, 'login']);

// Logout (requiere estar autenticado)
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum');

// Ruta para enviar enlace de reestablecimiento
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

//Ruta para restablecer contraseña
Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');
    
// Rutas para las colecciones
Route::middleware('auth:sanctum')->controller(ColeccionesController::class)->group(function() {
    Route::get('/colecciones', 'index');
    Route::post('/colecciones', 'store');
    Route::get('/colecciones/{id}', 'show');
    Route::put('/colecciones/{id}', 'update');
    Route::delete('/colecciones/{id}', 'destroy');
});

// Rutas para los contenidos de las colecciones
Route::middleware('auth:sanctum')->controller(ContenidoController::class)->group(function() {
    Route::get('/contenidos', 'index');
    Route::post('/contenidos', 'store');
    Route::get('/contenidos/{id}', 'show');
    Route::put('/contenidos/{id}', 'update');
    Route::delete('/contenidos/{id}', 'destroy');
});


// Ruta para enviar enlace de reestablecimiento
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

// Ruta para restablecer contraseña con token
Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');