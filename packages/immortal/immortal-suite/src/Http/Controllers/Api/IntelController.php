<?php

namespace Immortal\Suite\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Immortal\Suite\Jobs\DispatchDiscordEvent;
use Immortal\Suite\Services\IntelService;

class IntelController extends Controller
{
    public function recent(IntelService $intel)
    {
        if (!$intel->isAvailable()) {
            return response()->json([
                'data' => [],
                'status' => 'unavailable',
            ]);
        }

        return response()->json([
            'data' => $intel->recent(),
            'status' => 'ok',
        ]);
    }

    public function record(Request $request, IntelService $intel)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'severity' => 'required|string',
            'details' => 'nullable|array',
        ]);

        $event = $intel->record($data['title'], $data['severity'], $data['details'] ?? []);
        DispatchDiscordEvent::dispatch('intel_alert', $event->toArray());

        return response()->json(['status' => 'recorded', 'event' => $event]);
    }
}
