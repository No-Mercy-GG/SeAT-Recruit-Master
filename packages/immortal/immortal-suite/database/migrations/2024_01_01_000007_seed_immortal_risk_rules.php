<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rules = [
            [
                'key' => 'discord_missing',
                'name' => 'Discord identity not linked',
                'enabled' => true,
                'weight' => 15,
                'lookback_days' => 0,
                'thresholds' => json_encode([]),
            ],
            [
                'key' => 'alts_unconfirmed',
                'name' => 'Alts not confirmed',
                'enabled' => true,
                'weight' => 20,
                'lookback_days' => 0,
                'thresholds' => json_encode([]),
            ],
        ];

        foreach ($rules as $rule) {
            $exists = DB::table('immortal_risk_rules')->where('key', $rule['key'])->exists();
            if (!$exists) {
                DB::table('immortal_risk_rules')->insert($rule);
            }
        }
    }

    public function down(): void
    {
        DB::table('immortal_risk_rules')->whereIn('key', ['discord_missing', 'alts_unconfirmed'])->delete();
    }
};
