<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeletingsLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temp:deletings-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'automaticly deletings oppening documents logs after 6 months';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = now()->subMonths(6);
        $logs = \App\Models\Log::where('created_at', '<', $date)->get();
        foreach ($logs as $log) {
            $log->delete();
        }
        $this->info('Logs older than 6 months have been deleted successfully.');
    }
}
