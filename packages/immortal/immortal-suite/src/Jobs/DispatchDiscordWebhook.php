<?php

namespace Immortal\Suite\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Immortal\Suite\Models\Application;
use Immortal\Suite\Services\DiscordWebhookService;

class DispatchDiscordWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Application $application;
    public array $risk;

    public function __construct(Application $application, array $risk)
    {
        $this->application = $application;
        $this->risk = $risk;
    }

    public function handle(DiscordWebhookService $service): void
    {
        $service->applicantCompleted($this->application, $this->risk);
    }
}
