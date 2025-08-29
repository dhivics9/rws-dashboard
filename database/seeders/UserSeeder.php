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
                'password' => Hash::make('admin123456'),
            ],
            [
                'name' => 'Inputter User',
                'email' => 'inputter@example.com',
                'role' => 'inputter',
                'password' => Hash::make('inputter123456'),
            ],
            [
                'name' => 'General User',
                'email' => 'user@example.com',
                'role' => 'general',
                'password' => Hash::make('user123456'),
            ]
        ]);
    }
}
