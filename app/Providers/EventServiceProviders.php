<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Events\DocumentOpened;
use App\Listeners\LogDocumentOpening;

class EventServiceProviders extends ServiceProvider
{
    public function boot(): void
    {
        //
    }

    public function register(): void
    {
        //
    }

    protected $listen = [
       DocumentOpened::class => [
            LogDocumentOpening::class,
        ],
    ];

}
