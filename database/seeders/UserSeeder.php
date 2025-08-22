<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ],
            [
                'name' => 'Inputter User',
                'email' => 'inputter@example.com',
                'role' => 'inputter',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ],
            [
                'name' => 'General User',
                'email' => 'user@example.com',
                'role' => 'general',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        ]);
    }
}
