<?php

namespace Immortal\Suite\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Immortal\Suite\Services\AuditLogger;
use Immortal\Suite\Services\SettingService;

class SettingsController extends Controller
{
    public function index(SettingService $settings)
    {
        $data = $settings->all();

        return view('immortal-suite::settings.index', compact('data'));
    }

    public function update(Request $request, SettingService $settings, AuditLogger $audit)
    {
        $payload = $request->validate([
            'feature_flags' => 'array',
            'discord' => 'array',
            'contacts_thresholds' => 'array',
            'risk' => 'array',
            'intel_config' => 'nullable|string',
            'alts' => 'array',
            'api' => 'array',
            'application_questions' => 'nullable|string',
            'deny_reasons' => 'nullable|string',
        ]);

        foreach ($payload as $key => $value) {
            if ($key === 'application_questions' || $key === 'deny_reasons' || $key === 'intel_config') {
                $decoded = json_decode($value ?: '[]', true);
                $value = is_array($decoded) ? $decoded : [];
                if ($key === 'intel_config') {
                    $key = 'intel';
                }
            }
            $settings->set($key, $value);
        }

        $audit->log('settings_updated', ['keys' => array_keys($payload)]);

        return redirect()->back();
    }
}
