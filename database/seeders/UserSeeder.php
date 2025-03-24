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
        $users = [
            [
                "name" => "superadmin",
                "email" => "qu.mialon@laposte.net",
                "password" => bcrypt("superadmin"),
                "id" => 1,
            ],];
        $user_roles = [
            [
                "user_id" => 1,
                "role_id" => 3,
            ],
        ];

            foreach ($users as $user) {
                \App\Models\User::create($user);
                \App\Models\User::find($user['id'])->roles()->attach($user_roles[0]['role_id']);
            }
    }
}
