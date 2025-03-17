<?php
namespace App\Listeners;

use App\Events\DocumentOpened;
use App\Services\LogService;

class LogDocumentOpening
{
    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function handle(DocumentOpened $event)
    {
        $this->logService->addLog($event->documentId, $event->userId);
    }
}


