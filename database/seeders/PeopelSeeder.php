<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PeopelSeeder extends Seeder
{

    public function run()
    {
        $admin = User::create([
            'name' => 'admin',
            'phone' => '01094641332',
            'email' => 'admin@admin.com',
            'type' => 'admin',
            'verified' => 1,
            'device_id' => '122device_id',
            'fcm_token' => 'fcm_tokenfcm_tokenfcm_token',
            'password' => '123456',
        ]);

        $student= User::create([
            'name' => 'student',
            'phone' => '01201636129',
            'email' => 'student@gmail.com',
            'type' => 'student',
            'verified' => 1,
            'device_id' => '123device_id',
            'fcm_token' => 'fcm3_tokenfcm_tokenfcm_token',
            'password' => '123456',
        ]);

        $assistant= User::create([
            'name' => 'assistant',
            'phone' => '01111651415',
            'email' => 'assistant@gmail.com',
            'type' => 'assistant',
            'verified' => 1,
            'device_id' => '124device_id',
            'fcm_token' => 'fcm4_tokenfcm_tokenfcm_token',
            'password' => '123456',
        ]);
    }
}
