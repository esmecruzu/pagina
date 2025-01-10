<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{

    public function run()
    {
        User::create([
            'name' => 'Administrador',
            'apellido_p' => 'Sanch',
            'apellido_m' => 'Sanch',
            'telefono' => '5565656565',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role_id' => 1,  
        ]);
    }
}
