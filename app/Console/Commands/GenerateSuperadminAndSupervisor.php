<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSuperadminAndSupervisor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boot:generate_superadmin_and_supervisor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate superadmin and supervisor users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = [
            [
                'name' => 'superadmin',
                'email' => env('SUPERADMIN_MAIL'),
                'password' => bcrypt(env('SUPERADMIN_PASSWORD')),
                'role_id' => 2, // ID du rôle superadmin
            ],
            [
                'name' => 'supervisor',
                'email' => env('SUPERVISOR_MAIL'),
                'password' => bcrypt(env('SUPERVISOR_PASSWORD')),
                'role_id' => 13, // ID du rôle supervisor
            ],
        ];

        foreach ($users as $data) {
            $user = \App\Models\User::updateOrCreate(
                ['email' => $data['email']],
                collect($data)->except('role_id')->toArray()
            );

            $user->roles()->syncWithoutDetaching([$data['role_id']]);
        }

        $this->info('Superadmin and supervisor users have been generated successfully.');
    }
}
