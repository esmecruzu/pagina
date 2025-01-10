<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Horario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class CitasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');  
    }

    public function registrar(Request $request)
    {
        Log::info('Usuario autenticado al registrar cita:', ['user' => auth()->user()]);

        $request->validate([
            'motivo' => 'required|string|max:255',
            'horario_id' => 'required|exists:horarios,id,status,disponible',  
        ]);

        $horario = Horario::findOrFail($request->horario_id);
        if ($horario->status == 'ocupado') {
            return response()->json(['message' => 'Este horario ya está reservado'], 400);
        }

        $cita = Cita::create([
            'user_id' => Auth::id(),  
            'horario_id' => $horario->id,
            'motivo' => $request->motivo,
            'status' => 'pendiente',  
        ]);

        $horario->status = 'ocupado';
        $horario->save();

        return response()->json(['message' => 'Cita agendada con éxito', 'cita' => $cita], 201);
    }

    
    public function vercitas()
    {
        $citas = Auth::user()->citas()->with('horario')->get();
        return response()->json($citas);
    }

    
    public function modificarStatus(Request $request, $id)
    {
    Log::info('Usuario autenticado al modificar el status de cita:', ['user' => auth()->user()]);
    $cita = Cita::findOrFail($id);

    if (Auth::user()->role->name !== 'admin') {
        return response()->json(['message' => 'Solo el usuario Administrador puede actualizar el estado de tu cita'], 403);
    }
    $request->validate([
        'status' => 'required|in:aprobada,rechazada',  
    ]);

    $cita->status = $request->status;
    $cita->save();

    return response()->json(['message' => 'Estado de la cita actualizado', 'cita' => $cita]);
    }

    
    public function horariosDisponibles()
    {
        $horarios = Horario::where('status', 'disponible')->get();
        return response()->json($horarios);
    }
}
