<?php

namespace Immortal\Suite\Http\Controllers;

use Illuminate\Routing\Controller;
use Immortal\Suite\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        $logs = AuditLog::query()->latest()->paginate(50);

        return view('immortal-suite::audit.index', compact('logs'));
    }
}
