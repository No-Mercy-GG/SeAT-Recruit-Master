<?php

namespace Immortal\Suite\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Immortal\Suite\Models\RiskRule;

class RiskEngineController extends Controller
{
    public function index()
    {
        $rules = RiskRule::query()->orderBy('key')->get();

        return view('immortal-suite::risk.index', compact('rules'));
    }

    public function update(Request $request, RiskRule $rule)
    {
        $data = $request->validate([
            'enabled' => 'nullable|boolean',
            'weight' => 'required|integer|min:0|max:100',
            'lookback_days' => 'required|integer|min:0|max:3650',
        ]);

        $rule->enabled = (bool) ($data['enabled'] ?? false);
        $rule->weight = $data['weight'];
        $rule->lookback_days = $data['lookback_days'];
        $rule->save();

        return redirect()->back();
    }
}
