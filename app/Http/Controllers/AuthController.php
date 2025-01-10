<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function registrar(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'apellido_p' => 'required|string|max:255',
            'apellido_m' => 'required|string|max:255',
            'telefono' => 'required|string|max:15',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $role = Role::where('name', 'cliente')->first(); 

        $user = User::create([
            'name' => $request->name,
            'apellido_p' => $request->apellido_p,
            'apellido_m' => $request->apellido_m,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id, 
        ]);

        return response()->json(['message' => 'El usuario ha sido registrado', 'user' => $user], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
        
        $user = User::where('email', $request->email)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $user->createToken('API Token')->plainTextToken;

        $user->sessions()->create([
            'start_session' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);
        
        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'No estás autenticado'], 401);
        }

        $user = auth()->user();

        $session = Session::where('user_id', $user->id)
                                ->whereNull('end_session')
                                ->orderBy('last_activity', 'desc') 
                                ->first();

        if ($session) {
            $session->update([
                'last_activity' => now(),  
                'end_session' => now(),     
            ]);
        }

        auth()->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Cierre de sesión exitoso']);
    }


}
