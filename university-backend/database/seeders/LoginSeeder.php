<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('login')->insert([
            'username' => 'admin',
            'password' => Hash::make('admin112481632'), // Replace with your desired password
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}


// for send data in table login run in terminal --> ( php artisan db:seed --class=LoginSeeder )