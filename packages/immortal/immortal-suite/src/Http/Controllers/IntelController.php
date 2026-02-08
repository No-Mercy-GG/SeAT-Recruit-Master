<?php

namespace Immortal\Suite\Http\Controllers;

use Illuminate\Routing\Controller;
use Immortal\Suite\Services\IntelService;

class IntelController extends Controller
{
    public function index(IntelService $intel)
    {
        $events = $intel->isAvailable() ? $intel->recent() : collect();
        $availability = [
            'contacts' => $intel->contactSummary(),
            'home_space' => $intel->homeSpace(),
        ];

        return view('immortal-suite::intel.index', compact('events', 'availability'));
    }
}
