<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImageService;
use Illuminate\Support\Str;

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

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        parent::__construct(); // Important pour l'enregistrement de la commande

        $this->imageService = $imageService;
    }


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Début du nettoyage des images...");
        $this->imageService->deleteUnusedImages();
        $this->info("Nettoyage terminé !");
    }
}
