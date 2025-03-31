<?php

namespace App\View\Components\SearchBar;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DocumentSearchBar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.search-bar.document-search-bar');
    }
}
