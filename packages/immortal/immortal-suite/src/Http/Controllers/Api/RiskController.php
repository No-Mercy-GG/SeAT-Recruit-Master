<?php

namespace Immortal\Suite\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Immortal\Suite\Models\Application;
use Immortal\Suite\Services\RiskEngineService;

class RiskController extends Controller
{
    public function show(Application $application, RiskEngineService $riskEngine)
    {
        return response()->json($riskEngine->evaluate($application));
    }
}
