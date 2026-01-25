<?php

namespace Immortal\Suite\Http\Controllers;

use Illuminate\Routing\Controller;

class ComplianceController extends Controller
{
    public function index()
    {
        return view('immortal-suite::compliance.index');
    }
}
