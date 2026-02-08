<?php

namespace Immortal\Suite\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Immortal\Suite\Services\SettingService;

class ImmortalApiSignature
{
    public function handle(Request $request, Closure $next)
    {
        $settings = app(SettingService::class);
        $secret = $settings->get('discord.shared_secret');
        $timestamp = $request->header('X-Immortal-Timestamp');
        $signature = $request->header('X-Immortal-Signature');

        if (!$secret || !$timestamp || !$signature) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $payload = $timestamp . '|' . $request->method() . '|' . $request->path() . '|' . $request->getContent();
        $expected = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expected, $signature)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
