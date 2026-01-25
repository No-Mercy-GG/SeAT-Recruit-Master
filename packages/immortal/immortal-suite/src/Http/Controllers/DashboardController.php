<?php

namespace Immortal\Suite\Http\Controllers;

use Illuminate\Routing\Controller;
use Immortal\Suite\Models\Application;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Application::query()->count(),
            'new' => Application::query()->where('status', 'NEW')->count(),
            'screening' => Application::query()->where('status', 'SCREENING')->count(),
        ];

        return view('immortal-suite::dashboard.index', compact('stats'));
    }
}
