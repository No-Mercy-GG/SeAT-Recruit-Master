<?php

namespace Immortal\Suite\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Immortal\Suite\Jobs\DispatchDiscordEvent;
use Immortal\Suite\Jobs\DispatchDiscordWebhook;
use Immortal\Suite\Models\Application;
use Immortal\Suite\Models\ApplicationSession;
use Immortal\Suite\Models\ApplicationStatus;
use Immortal\Suite\Services\AuditLogger;
use Immortal\Suite\Services\RiskEngineService;
use Immortal\Suite\Services\SettingService;

class ApplicationsController extends Controller
{
    public function index()
    {
        $applications = Application::query()->latest()->paginate(25);

        return view('immortal-suite::applications.index', compact('applications'));
    }

    public function show(Application $application)
    {
        $settings = app(SettingService::class);
        $history = ApplicationStatus::query()
            ->where('application_id', $application->id)
            ->latest()
            ->get();
        $denyReasons = $settings->get('deny_reasons', []);

        return view('immortal-suite::applications.show', compact('application', 'history', 'denyReasons'));
    }

    public function start(Request $request, SettingService $settings)
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $data = $request->validate([
            'ticket' => 'required|string',
            'discord' => 'required|string',
            'guild' => 'required|string',
            'sig' => 'required|string',
        ]);

        $secret = $settings->get('discord.shared_secret');
        $expected = hash_hmac('sha256', "{$data['ticket']}|{$data['discord']}|{$data['guild']}", (string) $secret);

        if (!$secret || !hash_equals($expected, $data['sig'])) {
            abort(403, 'Invalid signature');
        }

        $session = ApplicationSession::query()->updateOrCreate(
            [
                'user_id' => Auth::id(),
                'ticket_id' => $data['ticket'],
            ],
            [
                'discord_user_id' => $data['discord'],
                'guild_id' => $data['guild'],
            ]
        );

        $application = Application::query()->firstOrCreate(
            [
                'user_id' => Auth::id(),
                'ticket_id' => $data['ticket'],
            ],
            [
                'status' => 'NEW',
                'discord_user_id' => $session->discord_user_id,
                'guild_id' => $session->guild_id,
            ]
        );

        $questions = $settings->get('application_questions', []);
        $checklist = $this->buildChecklist($application, $settings, $questions);

        return view('immortal-suite::applications.start', compact('application', 'checklist', 'questions'));
    }

    public function complete(Request $request, Application $application, RiskEngineService $riskEngine, AuditLogger $audit)
    {
        $settings = app(SettingService::class);
        $questions = $settings->get('application_questions', []);
        $answers = $request->input('application_data', []);
        $altsConfirmed = (bool) $request->boolean('alts_confirmed');

        $requiredMissing = collect($questions)
            ->filter(fn ($question) => !empty($question['required']))
            ->filter(fn ($question) => empty($answers[$question['id'] ?? '']))
            ->count();

        if ($requiredMissing > 0) {
            return redirect()->back()->withErrors(['application_data' => 'Please answer all required questions.']);
        }

        if ($settings->get('alts.require_confirmation', true) && !$altsConfirmed) {
            return redirect()->back()->withErrors(['alts_confirmed' => 'Please confirm all alts have been added.']);
        }

        $answers['alts_confirmed'] = $altsConfirmed;
        $application->application_data = $answers;
        $application->status = 'COMPLETED';
        $application->save();

        ApplicationStatus::query()->create([
            'application_id' => $application->id,
            'user_id' => Auth::id(),
            'action' => 'completed',
            'status' => $application->status,
        ]);

        $risk = $riskEngine->refreshFindings($application);
        DispatchDiscordWebhook::dispatch($application, $risk);

        $audit->log('application_completed', [
            'application_id' => $application->id,
        ]);

        return redirect()->route('immortal.applications.show', $application);
    }

    public function updateStatus(Request $request, Application $application, AuditLogger $audit)
    {
        $data = $request->validate([
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'deny_reason' => 'nullable|string',
        ]);

        $application->status = $data['status'];
        if ($data['status'] === 'DENIED' && !empty($data['deny_reason'])) {
            $application->application_data = array_merge($application->application_data ?? [], [
                'deny_reason' => $data['deny_reason'],
            ]);
        }
        $application->save();

        ApplicationStatus::query()->create([
            'application_id' => $application->id,
            'user_id' => Auth::id(),
            'action' => 'status_update',
            'status' => $data['status'],
            'notes' => $data['notes'] ?? $data['deny_reason'] ?? null,
        ]);

        DispatchDiscordEvent::dispatch('status_changed', [
            'status' => $data['status'],
            'notes' => $data['notes'] ?? $data['deny_reason'] ?? null,
        ], $application);

        $audit->log('application_status_updated', [
            'application_id' => $application->id,
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->back();
    }

    public function claim(Application $application, AuditLogger $audit)
    {
        $application->assigned_to = Auth::id();
        $application->save();

        ApplicationStatus::query()->create([
            'application_id' => $application->id,
            'user_id' => Auth::id(),
            'action' => 'claimed',
            'status' => $application->status,
        ]);

        $audit->log('application_claimed', [
            'application_id' => $application->id,
        ]);

        return redirect()->back();
    }

    public function addNote(Request $request, Application $application, AuditLogger $audit)
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

        $audit->log('application_note_added', [
            'application_id' => $application->id,
        ]);

        return redirect()->back();
    }

    private function buildChecklist(Application $application, SettingService $settings, array $questions): array
    {
        $requiredQuestions = collect($questions)
            ->filter(fn ($question) => !empty($question['required']))
            ->count();
        $answeredRequired = collect($questions)
            ->filter(fn ($question) => !empty($question['required']))
            ->filter(fn ($question) => !empty($application->application_data[$question['id'] ?? '']))
            ->count();
        $altsConfirmed = (bool) ($application->application_data['alts_confirmed'] ?? false);

        return [
            [
                'label' => 'Discord identity connected to SeAT',
                'complete' => (bool) $application->discord_user_id,
            ],
            [
                'label' => 'All alts added',
                'complete' => $altsConfirmed,
                'note' => 'Unable to verify automatically. Manual confirmation required.',
            ],
            [
                'label' => 'Required questions answered',
                'complete' => $requiredQuestions === $answeredRequired,
            ],
            [
                'label' => 'Doctrine skill checks completed',
                'complete' => !$settings->get('feature_flags.doctrine_checks', false),
                'optional' => true,
            ],
        ];
    }
}
