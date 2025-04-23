<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;

class GenerateCoreCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boot:generate-core-category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create the "public" category that everyone can access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Category::updateOrCreate(
            [
                'name' => 'public',
                'id' => 1,
                'role_id' => 1,
            ]
        );
    }
}
