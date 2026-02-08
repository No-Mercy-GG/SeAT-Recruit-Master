<?php

namespace Immortal\Suite\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Immortal\Suite\Models\Application;
use Immortal\Suite\Models\ApplicationStatus;

class ApplicantsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $query = Application::query();
        if ($status) {
            $query->where('status', $status);
        }

        return response()->json($query->latest()->get());
    }

    public function show(Application $application)
    {
        return response()->json($application);
    }

    public function compliance(Application $application)
    {
        return response()->json([
            'discord_linked' => (bool) $application->discord_user_id,
            'alts_complete' => null,
            'scopes_ok' => true,
        ]);
    }

    public function claim(Application $application)
    {
        $application->assigned_to = Auth::id();
        $application->save();

        ApplicationStatus::query()->create([
            'application_id' => $application->id,
            'user_id' => Auth::id(),
            'action' => 'claimed',
            'status' => $application->status,
        ]);

        return response()->json(['status' => 'claimed']);
    }

    public function setStatus(Request $request, Application $application)
    {
        $data = $request->validate([
            'status' => 'required|string',
        ]);

        $application->status = $data['status'];
        $application->save();

        ApplicationStatus::query()->create([
            'application_id' => $application->id,
            'user_id' => Auth::id(),
            'action' => 'status_update',
            'status' => $data['status'],
        ]);

        return response()->json(['status' => 'updated']);
    }

    public function addNote(Request $request, Application $application)
    {
        $data = $request->validate([
            'notes' => 'required|string',
        ]);

        $application->notes = trim(($application->notes ?? '') . "\n" . $data['notes']);
        $application->save();

        ApplicationStatus::query()->create([
            'application_id' => $application->id,
            'user_id' => Auth::id(),
            'action' => 'note_added',
            'status' => $application->status,
            'notes' => $data['notes'],
        ]);

        return response()->json(['status' => 'noted']);
    }
}
