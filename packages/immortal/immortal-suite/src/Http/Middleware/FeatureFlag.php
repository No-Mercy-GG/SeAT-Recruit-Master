<?php

namespace Immortal\Suite\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Immortal\Suite\Services\SettingService;

class FeatureFlag
{
    public function handle(Request $request, Closure $next, string $flag)
    {
        $settings = app(SettingService::class);
        if (!$settings->get('feature_flags.' . $flag, false)) {
            abort(404);
        }

        return $next($request);
    }
}
