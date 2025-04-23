<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BootApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boot:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call all cores commands to boot the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('boot:generate_cores_roles');
        $this->call('boot:generate_superadmin_and_supervisor');
        $this->call('boot:generate-core-category');
        $this->info('Application booted successfully.');
    }
}
