<?php

namespace App\Models;
use Illuminate\Support\Str;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    public $timestamps = false;

    const CREATED_AT = null;
    const UPDATED_AT = null;
    //use HasFactory;
    protected $fillable = [
        'user_id',
        'start_session',
        'end_session',
        'last_activity',
        'ip_address',
        'user_agent',
    ];

    protected $keyType = 'string'; 
    public $incrementing = false; 

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($session) {
            $session->id = (string) Str::uuid(); 
        });
    }
}
