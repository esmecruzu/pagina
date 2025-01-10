<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DialogflowService;

class ChatbotController extends Controller
{
    protected $dialogflowService;

    public function __construct(DialogflowService $dialogflowService)
    {
        $this->dialogflowService = $dialogflowService;
    }

    public function enviarMensaje(Request $request)
    {
        $sessionId = uniqid('chatbot_', true);
        $message = $request->input('message');

        if (!$message) {
            return response()->json(['error' => 'El mensaje es obligatorio'], 400);
    }
    try {
        $response = $this->dialogflowService->detectIntent($sessionId, $message);
        return response()->json($response);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al procesar el mensaje con Dialogflow', 'details' => $e->getMessage()], 500);
    }
}
}