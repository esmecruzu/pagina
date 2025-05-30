<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\ChatbotController;
use App\Http\Middleware\Roles;
use App\Models\Role;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Registro de usuarios
Route::post('/registrar', [AuthController::class, 'registrar']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/registrar/cita', [CitasController::class, 'registrar']);
    Route::get('/ver/citas', [CitasController::class, 'vercitas']);
    Route::put('/cita/{id}/cancelar', [CitasController::class, 'cancelarCita']);
});

Route::get('/horarios/disponibles', [CitasController::class, 'horariosDisponibles']);


Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::post('/horarios', [HorarioController::class, 'store']);
        Route::get('/horarios', [HorarioController::class, 'index']);
        Route::delete('eliminar/horario/{id}', [HorarioController::class, 'eliminarHorario']);
        Route::put('/horarios/{id}/status', [HorarioController::class, 'updateStatus']);
        Route::put('/modificarcitas/{id}', [CitasController::class, 'modificarStatus']);
        Route::get('/citas/canceladas', [CitasController::class, 'verCitasCanceladas']);
        Route::get('/ver/AllCitas', [CitasController::class, 'verAllCitas']);
    });
});

Route::post('/chatbot', [ChatbotController::class, 'handle']);



