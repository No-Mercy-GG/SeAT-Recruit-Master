<?php

namespace Immortal\Suite\Services;

use Illuminate\Support\Facades\Http;
use Immortal\Suite\Models\Application;
use Immortal\Suite\Services\SettingService;

class DiscordWebhookService
{
    public function dispatch(string $event, array $payload): void
    {
        $settings = app(SettingService::class);
        $url = $settings->get('discord.webhook_url');
        $secret = $settings->get('discord.shared_secret');
        $enabled = $settings->get('discord.event_toggles.' . $event, false);

        if (!$url || !$secret || !$enabled) {
            return;
        }

        $signature = hash_hmac('sha256', json_encode($payload), $secret);

        Http::retry(3, 200)
            ->withHeaders(['X-Immortal-Signature' => $signature, 'X-Immortal-Event' => $event])
            ->post($url, $payload);
    }

    public function applicantCompleted(Application $application, array $risk): void
    {
        $payload = [
            'event' => 'application_completed',
            'ticket_id' => $application->ticket_id,
            'discord_user_id' => $application->discord_user_id,
            'application_id' => $application->id,
            'risk_score' => $risk['score'],
            'top_findings' => array_slice($risk['findings'], 0, 3),
            'dossier_url' => route('immortal.dossier', $application),
        ];

        $this->dispatch('application_completed', $payload);
    }

    public function statusChanged(Application $application, string $status, ?string $notes = null): void
    {
        $payload = [
            'event' => 'status_changed',
            'application_id' => $application->id,
            'ticket_id' => $application->ticket_id,
            'status' => $status,
            'notes' => $notes,
        ];

        $this->dispatch('status_changed', $payload);
    }

    public function intelAlert(array $intel): void
    {
        $payload = [
            'event' => 'intel_alert',
            'intel' => $intel,
        ];

        $this->dispatch('intel_alert', $payload);
    }
}
