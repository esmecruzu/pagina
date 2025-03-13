<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('horario_id');
            $table->string('motivo');
            $table->enum('status', ['pendiente', 'aprobada', 'rechazada','cancelada'])->default('pendiente');
            $table->timestamps();
        });


    }


    public function down()
    {
        Schema::dropIfExists('citas');
    }
};
