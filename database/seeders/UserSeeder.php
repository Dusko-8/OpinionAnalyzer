<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::create([
            'name' => 'Dusan', // Replace with the desired name
            'email' => 'sluka.d69@gmail.com',
            'password' => Hash::make('Password'), // It's important to hash the password
        ]);
    }
}
