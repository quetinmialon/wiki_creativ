<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeletingExpiredPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temp:deleting-expired-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deleting expired and denied permissions after 1 month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = now()->subMonths(1);
        $permissions = \App\Models\Permission::where(function ($query) use ($date): void {
            $query->where('created_at', '<', $date)
                  ->whereIn('status', ['denied', 'expired']);
        })->get();
        foreach ($permissions as $permission) {
            $permission->delete();
        }
        $this->info('Expired and denied permissions have been deleted successfully.');
    }
}
