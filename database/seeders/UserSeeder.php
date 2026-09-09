<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //[key, connect , value]
        \App\Models\User::create([
            'name' => 'Test Owner',
            'email' => 'owner@scandine.com',
            'password' => bcrypt('password'),
            'role' => 'owner',
        ]);

        \App\Models\User::create([
            'name' => 'Test Manager',
            'email' => 'manager@scandine.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
        ]);

        \App\Models\User::create([
            'name' => 'Test Staff',
            'email' => 'staff@scandine.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);
    }
}
