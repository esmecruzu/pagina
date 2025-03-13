<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
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
        try {
            Log::info('Intentando registrar un nuevo horario', [
                'user' => auth()->user(),
                'data' => $request->all(),
            ]);

            $validatedData = $request->validate([
                'fecha' => 'required|date',
                'hora' => 'required|date_format:H:i',
            ]);

            $horario = Horario::create([
                'fecha' => $validatedData['fecha'],
                'hora' => $validatedData['hora'],
                'status' => 'disponible',
            ]);

            Log::info('Horario registrado con éxito', ['horario' => $horario]);

            return response()->json([
                'success' => true,
                'message' => 'Horario registrado exitosamente',
                'data' => $horario,
            ], 201);

        } catch (ValidationException $e) {
            Log::warning('Error de validación al crear horario', ['errors' => $e->errors()]);

            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);

        } catch (QueryException $e) {
            Log::error('Error en la base de datos al crear horario', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el horario en la base de datos',
                'error' => $e->getMessage(),
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error inesperado al crear horario', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado',
                'error' => $e->getMessage(),
            ], 500);
        }
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

    public function eliminarHorario($id)
{
    try {
        if (Auth::user()->role->name !== 'admin') {
            return response()->json(['message' => 'Solo el usuario Administrador puede eliminar horarios'], 403);
        }
        $horario = Horario::findOrFail($id);
        $horario->delete();
        return response()->json([
            'success' => true,
            'message' => 'Horario eliminado exitosamente',
        ], 200);

    } catch (ModelNotFoundException $e) {
        Log::error('Horario no encontrado', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'El horario no existe',
        ], 404);

    } catch (Exception $e) {
        Log::error('Error al eliminar el horario', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Ocurrió un error al eliminar el horario',
        ], 500);
    }
}

}
