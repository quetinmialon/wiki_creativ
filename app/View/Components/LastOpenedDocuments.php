<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\LogService;

class LastOpenedDocuments extends Component
{
    protected $logService;
    /**
     * Create a new component instance.
     */
    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
{
        $logs = $this->logService->getLastOpenedDocuments(5);
        return view('components.last-opened-documents', compact('logs'));

    }
}
