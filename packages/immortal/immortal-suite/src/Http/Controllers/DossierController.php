<?php

namespace Immortal\Suite\Http\Controllers;

use Illuminate\Routing\Controller;
use Immortal\Suite\Models\Application;
use Immortal\Suite\Models\RiskFinding;
use Immortal\Suite\Services\RiskEngineService;

class DossierController extends Controller
{
    public function show(Application $application, RiskEngineService $riskEngine)
    {
        $risk = $riskEngine->evaluate($application);
        $findings = RiskFinding::query()->where('application_id', $application->id)->get();

        return view('immortal-suite::dossier.show', compact('application', 'risk', 'findings'));
    }
}
