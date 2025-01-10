<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Role; 

class Roles
{
   
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Asegurarnos de que el usuario esté autenticado
        $user = Auth::user();

        // Agregar un log para verificar el usuario autenticado
        Log::info('Usuario autenticado en middleware Roles:', ['user' => $user]);

        // Verificamos si el usuario tiene el rol adecuado
        if ($user && $user->role->name === $role) {
            return $next($request);
        }

        // Si el rol no coincide, o el usuario no está autenticado, accedo denegado
        Log::warning('Acceso denegado para el usuario:', ['user' => $user]);

        return response()->json(['message' => 'Acceso denegado'], 403);
    }
}
