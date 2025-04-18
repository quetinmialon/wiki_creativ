<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateCoreRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boot:generate_cores_roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate cores roles and admin associated roles';

    /**
     * Execute the console command.
     */
    public function handle()
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
            ['name'=> 'supervisor'],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::updateOrCreate(['name' => $role['name']], $role);
        }

        $this->info('Roles have been generated successfully.');
    }
}
