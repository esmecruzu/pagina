<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Intent;
use Illuminate\Support\Facades\DB;


class IntentSeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('intents')->insert([
            ['pregunta' => 'hola', 'respuesta' => '¡Hola! ¿En qué puedo ayudarte?'],
            ['pregunta' => 'adiós', 'respuesta' => '¡Adiós! Que tengas un buen día.'],
            ['pregunta' => 'ayuda', 'respuesta' => 'Claro, puedo ayudarte con tus preguntas sobre el sistema.']
        ]);
    }
}
