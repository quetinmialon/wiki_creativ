<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use App\Models\Log;
use Carbon\Carbon;

class OpenedDocumentEvolution extends Component
{
    public $dates;
    public $counts;

    public function __construct()
    {
        // Récupérer les logs des 7 derniers jours groupés par jour
        $logs = Log::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $this->dates = [];
        $this->counts = [];

        // Générer une liste des 7 derniers jours
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->toDateString();
            $this->dates[] = $date;
            $this->counts[] = $logs->firstWhere('date', $date)->total ?? 0;
        }
    }

    public function render()
    {
        return view('components.admin.opened-document-evolution');
    }
}

