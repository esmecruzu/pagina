<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Intent;


class IntentFactory extends Factory
{
    protected $model = Intent::class;

    public function definition(): array
    {
        return [
            'pregunta' => $this->faker->sentence(),  // Genera una pregunta falsa
            'respuesta' => $this->faker->paragraph(),  // Genera una respuesta falsa
        ];
    }

    public function specificQuestions()
    {
        return $this->state(function (array $attributes) {
            $questionsAndResponses = [
                [
                    'pregunta' => 'Hola',
                    'respuesta' => '¡Hola! Bienvenido, ¿En qué puedo ayudarte?',
                ],
                [
                    'pregunta' => '¿Cómo estás?',
                    'respuesta' => '¡Estoy bien, gracias por preguntar!',
                ],
                [
                    'pregunta' => '¿Qué puedes hacer?',
                    'respuesta' => 'Puedo ayudarte a responder preguntas sobre el sistema.',
                ],
                [
                    'pregunta' => '¿Quién eres?',
                    'respuesta' => 'Soy un chatbot creado para asistirte.',
                ],
                [
                    'pregunta' => '¿Cuál es tu propósito?',
                    'respuesta' => 'Mi propósito es ayudarte con preguntas frecuentes.',
                ],
                [
                    'pregunta' => '¿Dónde estás ubicado?',
                    'respuesta' => 'Estoy en el servidor de tu aplicación.',
                ],
            ];

            // Selecciona una de las preguntas y respuestas específicas
            $randomQuestion = $questionsAndResponses[array_rand($questionsAndResponses)];

            return [
                'pregunta' => $randomQuestion['pregunta'],
                'respuesta' => $randomQuestion['respuesta'],
            ];
        });
    }
}
