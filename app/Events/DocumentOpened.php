<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentOpened
{
    use Dispatchable, SerializesModels;

    public $documentId;
    public $userId;

    public function __construct($documentId, $userId)
    {
        $this->documentId = $documentId;
        $this->userId = $userId;
    }
}

