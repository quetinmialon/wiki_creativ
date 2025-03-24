<?php

namespace App\View\Components;

use App\Services\FavoriteService;
use Illuminate\View\Component;

class FavoriteList extends Component
{
    protected $favoriteService;
    /**
     * Create a new component instance.
     */
    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }



    /**
     * Get the view / contents that represent the component.
     */

    public function render()
    {
        $favorites = $this->favoriteService->getUserFavorites();
        return view('components.favorite-list', compact('favorites'));
    }

}
