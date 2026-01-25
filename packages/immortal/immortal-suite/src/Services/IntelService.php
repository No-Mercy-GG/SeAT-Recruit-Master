<?php

namespace Immortal\Suite\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Immortal\Suite\Models\IntelEvent;
use Immortal\Suite\Services\SettingService;

class IntelService
{
    public function isAvailable(): bool
    {
        return Schema::hasTable('immortal_intel_events') && !empty($this->contactTables());
    }

    public function recent(int $limit = 25)
    {
        return IntelEvent::query()->latest()->limit($limit)->get();
    }

    public function contactSummary(): array
    {
        $tables = $this->contactTables();
        if (empty($tables)) {
            return [
                'available' => false,
                'data' => [],
            ];
        }

        $contacts = [];
        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'standing')) {
                continue;
            }
            $contacts[$table] = DB::table($table)->select('standing')->limit(250)->get();
        }

        return [
            'available' => true,
            'tables' => array_keys($contacts),
            'counts' => array_map(fn ($items) => count($items), $contacts),
        ];
    }

    public function homeSpace(): array
    {
        $intel = $this->settings()->get('intel', []);
        $sources = $intel['home_sources'] ?? [];
        $systems = [];

        if (($sources['sov'] ?? false) && Schema::hasTable('sovereignty_structures') && Schema::hasColumn('sovereignty_structures', 'solar_system_id')) {
            $systems = array_merge($systems, DB::table('sovereignty_structures')->pluck('solar_system_id')->unique()->toArray());
        }

        if (($sources['structures'] ?? false) && Schema::hasTable('corporation_structures') && Schema::hasColumn('corporation_structures', 'solar_system_id')) {
            $systems = array_merge($systems, DB::table('corporation_structures')->pluck('solar_system_id')->unique()->toArray());
        }

        return [
            'available' => !empty($systems),
            'systems' => array_values(array_unique($systems)),
        ];
    }

    public function record(string $title, string $severity, array $details = []): IntelEvent
    {
        return IntelEvent::query()->create([
            'title' => $title,
            'severity' => $severity,
            'details' => $details,
        ]);
    }

    private function contactTables(): array
    {
        $intel = $this->settings()->get('intel', []);
        $candidates = $intel['contact_table_candidates'] ?? [];

        return array_values(array_filter($candidates, fn ($table) => Schema::hasTable($table)));
    }

    private function settings(): SettingService
    {
        return app(SettingService::class);
    }
}
