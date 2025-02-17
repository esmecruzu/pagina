<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Intent;

class ChatbotController extends Controller
{
    public function handle(Request $request)
    {
        $userMessage = strtolower(trim($request->input('mensaje', '')));
        $keywords = explode(' ', $userMessage);

        $response = Intent::query()
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('pregunta', 'LIKE', '%' . $keyword . '%');
                }
            })
            ->value('respuesta');

        if (!$response) {
            $response = 'Lo siento. ¿Podrías reformular tu pregunta o ser más específico?';
        }

        return response()->json(['respuesta' => $response]);
    }

}
