<?php

namespace Immortal\Suite\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Immortal\Suite\Services\SettingService;

class ImmortalApiAdminToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-Immortal-Admin-Token') ?? $request->query('admin_token');
        $settings = app(SettingService::class);
        $expected = $settings->get('api.admin_token');

        if (!$expected || $token !== $expected) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
