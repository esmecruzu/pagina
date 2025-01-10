<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();  
            $table->date('fecha'); 
            $table->time('hora');  
            $table->enum('status', ['disponible', 'ocupado'])->default('disponible'); 
            $table->timestamps();  
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('horarios');
    }
};
