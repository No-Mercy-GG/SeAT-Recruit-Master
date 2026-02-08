<?php

namespace Immortal\Suite\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Immortal\Suite\Models\Application;
use Immortal\Suite\Services\DiscordWebhookService;

class DispatchDiscordEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $event;
    public array $payload;
    public ?Application $application;

    public function __construct(string $event, array $payload, ?Application $application = null)
    {
        $this->event = $event;
        $this->payload = $payload;
        $this->application = $application;
    }

    public function handle(DiscordWebhookService $service): void
    {
        if ($this->event === 'status_changed' && $this->application) {
            $service->statusChanged($this->application, $this->payload['status'] ?? '', $this->payload['notes'] ?? null);
            return;
        }

        if ($this->event === 'intel_alert') {
            $service->intelAlert($this->payload);
            return;
        }

        $service->dispatch($this->event, $this->payload);
    }
}
