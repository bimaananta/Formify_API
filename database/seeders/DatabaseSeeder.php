<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::create([
            'name' => 'User 1',
            'email' => 'user1@webtech.id ',
            'password' => "password1"
        ]);
        
        \App\Models\User::create([
            'name' => 'User 2',
            'email' => 'user2@webtech.id',
            'password' => "password2"
        ]);

        \App\Models\User::create([
            'name' => 'User 3',
            'email' => 'user3@worldskills.org',
            'password' => "password3"
        ]);
    }
}
