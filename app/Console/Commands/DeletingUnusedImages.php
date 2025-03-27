<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeletingUnusedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temp:deleting-unused-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deleting images that has been downloaded from front but aren\'t used in documents';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // hard to do so we'll see after
    }
}
