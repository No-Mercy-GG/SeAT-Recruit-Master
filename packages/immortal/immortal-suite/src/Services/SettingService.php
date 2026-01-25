<?php

namespace Immortal\Suite\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Immortal\Suite\Models\Setting;

class SettingService
{
    private string $cacheKey = 'immortal.settings';

    public function all(): array
    {
        return Cache::remember($this->cacheKey, config('immortal-suite.cache_ttl', 300), function () {
            $defaults = config('immortal-suite.defaults', []);
            $stored = Setting::query()->get()->pluck('value', 'key')->toArray();

            return array_replace_recursive($defaults, $stored);
        });
    }

    public function get(string $key, $default = null)
    {
        return Arr::get($this->all(), $key, $default);
    }

    public function set(string $key, $value): void
    {
        Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget($this->cacheKey);
    }
}
