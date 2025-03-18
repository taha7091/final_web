<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'CodeAstro',
                'email' => 'admin@mail.com',
                'password' => Hash::make('codeastro.com'),
                'role' => 'admin',
                'status' => 'active'
            ],
            [
                'name' => 'Customer A',
                'email' => 'customer@mail.com',
                'password' => Hash::make('codeastro.com'),
                'role' => 'user',
                'status' => 'active'
            ]
        ];

        DB::table('users')->insert($data);
    }
}
