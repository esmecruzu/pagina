<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Exception;
use App\Models\Cita;
use App\Models\Horario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



class CitasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    //Registrar una cita

    public function registrar(Request $request)
{
    try {
        Log::info('Intentando registrar una cita', [
            'user' => auth()->user(),
            'data' => $request->all(),
        ]);
        $validatedData = $request->validate([
            'motivo' => 'required|string|max:255',
            'horario_id' => 'required|exists:horarios,id,status,disponible',
        ]);

        $horario = Horario::findOrFail($validatedData['horario_id']);

        if ($horario->status !== 'disponible') {
            Log::warning('Intento de agendar en un horario ocupado', ['horario_id' => $horario->status]);

            return response()->json([
                'success' => false,
                'message' => 'Este horario ya está ocupado',
            ], 400);
        }

        $cita = Cita::create([
            'user_id' => Auth::id(),
            'horario_id' => $horario->id,
            'motivo' => $validatedData['motivo'],
            'status' => 'pendiente',
        ]);

        $horario->update(['status' => 'ocupado']);

        Log::info('Cita registrada con éxito', ['cita' => $cita]);

        return response()->json([
            'success' => true,
            'message' => 'Cita agendada con éxito',
            'data' => $cita,
        ], 201);

    } catch (ValidationException $e) {
        Log::warning('Error de validación al registrar cita', ['errors' => $e->errors()]);

        return response()->json([
            'success' => false,
            'message' => 'Error al registrar la cita',
            'errors' => $e->errors(),
        ], 422);

    } catch (ModelNotFoundException $e) {
        Log::error('Horario no encontrado', ['error' => $e->getMessage()]);

        return response()->json([
            'success' => false,
            'message' => 'El horario seleccionado no existe',
        ], 404);

    } catch (QueryException $e) {
        Log::error('Error en la base de datos al registrar cita', ['error' => $e->getMessage()]);

        return response()->json([
            'success' => false,
            'message' => 'Error al registrar la cita en la base de datos',
            'error' => $e->getMessage(),
        ], 500);

    } catch (\Exception $e) {
        Log::error('Error inesperado al registrar cita', ['error' => $e->getMessage()]);

        return response()->json([
            'success' => false,
            'message' => 'Ocurrió un error al registrar la cita',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    //Ver citas agendadas
    public function vercitas()
    {
        try {
            $citas = DB::table('citas')
                ->join('horarios', 'citas.horario_id', '=', 'horarios.id')
                ->where('citas.user_id', Auth::id())
                ->select('citas.*', 'horarios.fecha', 'horarios.hora', 'horarios.status') 
                ->get();
                if ($citas->isEmpty()) {
                    return response()->json(['message' => 'No tienes citas registradas'], 404);
                }
                return response()->json($citas);

            } catch (QueryException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener las citas: ' . $e->getMessage(),
                ], 500);

            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error inesperado: ' . $e->getMessage(),
                ], 500);
            }
        }

    //El administrador cambia el status de cita 'aprobar/rechazar'
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

    //Ver horarios con status 'disponible'
    public function horariosDisponibles()
    {
        $horarios = Horario::where('status', 'disponible')->get();
        return response()->json($horarios);
    }

    //Cancelar una cita
    public function cancelarCita(Request $request, $id)
    {
        $user = auth()->user();
        $cita = Cita::where('id', $id)->where('user_id', $user->id)->first();

        if (!$cita) {
            return response()->json(['error' => 'Cita no encontrada o no tienes permiso para cancelarla.'], 404);
        }

        if ($cita->status === 'cancelada') {
            return response()->json(['message' => 'La cita ya ha sido cancelada.'], 400);
        }

        $cita->update(['status' => 'cancelada']);
        $horario = Horario::find($cita->horario_id);
        if ($horario) {
            $horario->update(['status' => 'disponible']);
        }

        return response()->json(['message' => 'Cita cancelada exitosamente.']);
    }

    //Ver las citas canceladas
    public function verCitasCanceladas()
    {
        if (Auth::user()->role->name !== 'admin') {
            return response()->json(['message' => 'Solo el usuario Administrador puede ver las citas canceladas'], 403);
        }
        $citas = Cita::where('status', 'cancelada')->get();
        return response()->json($citas);
    }

    //Ver todas las citas
    public function verAllCitas()
    {
        if (Auth::user()->role->name !== 'admin') {
            return response()->json(['message' => 'Solo el usuario Administrador puede ver todas las citas'], 403);
        }
        $citas = Cita::with(['user', 'horario'])->get();
        return response()->json($citas);
    }
}
