<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateExpiredPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temp:update-expired-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'change the status of expired permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = now();
        $permissions = \App\Models\Permission::where('created_at', '<', $date)->where('status', 'approved')->get();
        foreach ($permissions as $permission) {
            $permission->status = 'expired';
            $permission->save();
        }
        $this->info('Expired permissions have been updated successfully.');
    }
}
