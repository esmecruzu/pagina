<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = ['fecha', 'hora', 'status'];

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }
}
