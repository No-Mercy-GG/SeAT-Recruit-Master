<?php

namespace Immortal\Suite\Services;

use Immortal\Suite\Models\Application;
use Immortal\Suite\Models\RiskFinding;
use Immortal\Suite\Models\RiskRule;

class RiskEngineService
{
    public function evaluate(Application $application): array
    {
        $rules = RiskRule::query()->where('enabled', true)->get();
        $totalScore = 0;
        $findings = [];

        foreach ($rules as $rule) {
            $finding = $this->runRule($rule, $application);
            if ($finding) {
                $totalScore += $finding['score'];
                $findings[] = $finding;
            }
        }

        $totalScore = min(100, max(0, $totalScore));

        return [
            'score' => $totalScore,
            'tier' => $this->tier($totalScore),
            'recommendation' => $this->recommendation($totalScore),
            'confidence' => $this->confidence($application),
            'findings' => $findings,
        ];
    }

    public function refreshFindings(Application $application): array
    {
        RiskFinding::query()->where('application_id', $application->id)->delete();
        $evaluation = $this->evaluate($application);

        foreach ($evaluation['findings'] as $finding) {
            RiskFinding::query()->create([
                'application_id' => $application->id,
                'rule_key' => $finding['rule_key'],
                'severity' => $finding['severity'],
                'summary' => $finding['summary'],
                'score' => $finding['score'],
                'details' => $finding['details'],
            ]);
        }

        return $evaluation;
    }

    private function runRule(RiskRule $rule, Application $application): ?array
    {
        if ($rule->key === 'discord_missing' && $application->discord_user_id) {
            return null;
        }

        if ($rule->key === 'alts_unconfirmed' && ($application->application_data['alts_confirmed'] ?? false)) {
            return null;
        }

        $details = [
            'rule' => $rule->key,
            'message' => 'Data unavailable; rule ready for integration.',
        ];

        if ($rule->key === 'discord_missing') {
            $details = [
                'rule' => $rule->key,
                'message' => 'Discord identity not linked to SeAT.',
            ];
        }

        if ($rule->key === 'alts_unconfirmed') {
            $details = [
                'rule' => $rule->key,
                'message' => 'Applicant did not confirm all alts.',
            ];
        }

        return [
            'rule_key' => $rule->key,
            'severity' => $this->severity($rule->weight),
            'summary' => $rule->name,
            'score' => (int) $rule->weight,
            'details' => $details,
        ];
    }

    private function tier(int $score): string
    {
        if ($score >= 70) {
            return 'red';
        }

        if ($score >= 40) {
            return 'amber';
        }

        return 'green';
    }

    private function recommendation(int $score): string
    {
        if ($score >= 90) {
            return 'Deny';
        }

        if ($score >= 70) {
            return 'Director Review';
        }

        if ($score >= 40) {
            return 'Interview';
        }

        return 'Accept';
    }

    private function confidence(Application $application): string
    {
        return $application->discord_user_id ? 'medium' : 'low';
    }

    private function severity(int $weight): string
    {
        if ($weight >= 30) {
            return 'critical';
        }
        if ($weight >= 20) {
            return 'high';
        }
        if ($weight >= 10) {
            return 'medium';
        }
        return 'low';
    }
}
