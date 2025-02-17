<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Intent;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intents', function (Blueprint $table) {
            $table->id();
            $table->string('pregunta')->unique(); 
            $table->text('respuesta'); 
            $table->timestamps();
        });

        //Intent::factory()->specificQuestions()->create();
    }

    public function down(): void
    {
        Schema::dropIfExists('intents');
    }
};
