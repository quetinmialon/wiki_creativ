<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ["name" => "default"],
            ["name" => "superadmin"],
            ["name" => "pédagogie"],
            ["name" => "Admin pédagogie"],
            ["name" => "commerciaux"],
            ["name" => "Admin commerciaux"],
            ["name" => "qualité"],
            ["name" => "Admin qualité"],
            ["name" => "financier"],
            ["name" => "Admin financier"],
            ["name" => "administratif"],
            ["name" => "Admin administratif"],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::create($role);
        }
    }
}
