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
                'email' => config('credential.superadmin_email'),
                'password' => bcrypt(config('credential.superadmin_password')),
                'role_id' => [2,1]
            ],
            [
                'name' => 'supervisor',
                'email' => config('credential.supervisor_email'),
                'password' => bcrypt(config('credential.supervisor_password')),
                'role_id' => [13]
            ],
        ];

        foreach ($users as $data) {
            $user = \App\Models\User::updateOrCreate(
                ['email' => $data['email']],
                collect($data)->except('role_id')->toArray()
            );

            $user->roles()->syncWithoutDetaching($data['role_id']);
        }

        $this->info('Superadmin and supervisor users have been generated successfully.');
    }
}
