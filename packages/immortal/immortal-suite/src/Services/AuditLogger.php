<?php

namespace Immortal\Suite\Services;

use Illuminate\Support\Facades\Auth;
use Immortal\Suite\Models\AuditLog;

class AuditLogger
{
    public function log(string $action, array $context = []): void
    {
        AuditLog::query()->create([
            'user_id' => Auth::id(),
            'action' => $action,
            'context' => $context,
        ]);
    }
}
