<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Horario;


class HorarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function index()
    {
        $horarios = Horario::where('status', 'disponible')->get();
        return response()->json($horarios);
    }

    public function store(Request $request)
    {
        Log::info('Usuario autenticado al crear horario:', ['user' => auth()->user()]);
        $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
        ]);

        $hora = Horario::create([
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'status' => 'disponible',
        ]);

        return response()->json($hora, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        Log::info('Usuario autenticado al modificar el status de horario:', ['user' => auth()->user()]);
        $hora = Horario::findOrFail($id);

        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'status' => 'required|in:disponible,ocupado',
        ]);

        $hora->status = $request->status;
        $hora->save();

        return response()->json(['message' => 'Estado del horario actualizado', 'hora' => $hora]);
    }
    
}
