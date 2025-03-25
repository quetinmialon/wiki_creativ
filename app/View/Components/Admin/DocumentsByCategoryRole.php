<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use App\Models\Category;

class DocumentsByCategoryRole extends Component
{
    public $labels;
    public $data;

    public function __construct()
    {
        $categories = Category::withCount('documents')->get();
        $this->labels = $categories->map(fn($cat) => $cat->name . ' (' . $cat->role->name . ')')->toArray();
        $this->data = $categories->map(fn($cat) => $cat->documents_count)->toArray();
    }

    public function render()
    {
        return view('components.admin.documents-by-category-role', [
            'labels' => $this->labels,
            'data' => $this->data,
        ]);
    }
}


