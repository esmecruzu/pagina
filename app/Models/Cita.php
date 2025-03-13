<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = ['id','user_id', 'horario_id', 'motivo', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }
}
