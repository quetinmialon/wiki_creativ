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
                'id' => 1,
                'name' => 'superadmin',
                'email' => env('SUPERADMIN_MAIL'),
                'password' => bcrypt(env('SUPERADMIN_PASSWORD')),
                'role_id' => 2, // ID du rôle superadmin
            ],
            [
                'id' => 2,
                'name' => 'supervisor',
                'email' => env('SUPERVISOR_MAIL'),
                'password' => bcrypt(env('SUPERVISOR_PASSWORD')),
                'role_id' => 13, // ID du rôle supervisor
            ],
        ];

        foreach ($users as $data) {
            $user = \App\Models\User::updateOrCreate(
                ['id' => $data['id']],
                collect($data)->except('role_id')->toArray()
            );

            $user->roles()->syncWithoutDetaching([$data['role_id']]);
        }
    }
}
