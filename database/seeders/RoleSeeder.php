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
            ["name" => "administratif"],
            ["name" => "default"],
            ["name" => "superadmin"],
            ["name" => "pédagogie"],
            ["name" => "commerciaux"],
            ["name" => "qualité"],
            ["name" => "financier"],
            ["name" => "Admin pédago"],
            ["name" => "Admin commerciaux"],
            ["name" => "Admin qualité"],
            ["name" => "Admin financier"],
            ["name" => "Admin administratif"],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::create($role);
        }
    }
}
