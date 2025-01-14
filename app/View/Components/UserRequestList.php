<?php

namespace App\View\Components;

use App\Models\User\UserRequest;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserRequestList extends Component
{
    /**
     * Create a new component instance.
     */

    public $userRequests;

    public function __construct()
    {
        $this->userRequests = UserRequest::all();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {

        return view(view: 'components.user_request_list', data : [$this->userRequests]);
    }
}
