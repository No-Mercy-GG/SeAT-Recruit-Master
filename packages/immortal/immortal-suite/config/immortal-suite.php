<?php

return [
    'cache_ttl' => 300,
    'defaults' => [
        'feature_flags' => [
            'applications' => true,
            'dossier' => true,
            'risk_engine' => true,
            'discord' => true,
            'intel' => true,
            'compliance' => true,
            'doctrine_checks' => false,
        ],
        'alts' => [
            'mode' => 'manual',
            'require_confirmation' => true,
        ],
        'discord' => [
            'webhook_url' => null,
            'shared_secret' => null,
            'event_toggles' => [
                'application_completed' => true,
                'applicant_flagged' => true,
                'status_changed' => true,
                'intel_alert' => true,
            ],
        ],
        'contacts_thresholds' => [
            'blue_min' => 5,
            'hostile_max' => -5,
        ],
        'intel' => [
            'contact_table_candidates' => [
                'corporation_contacts',
                'alliance_contacts',
            ],
            'home_sources' => [
                'sov' => true,
                'structures' => true,
            ],
        ],
        'risk' => [
            'routing' => [
                'director_review' => 70,
                'deny' => 90,
            ],
        ],
        'application_questions' => [],
        'deny_reasons' => [
            'Missing intel',
            'Hostile standings match',
            'Alt disclosure incomplete',
        ],
        'api' => [
            'token' => null,
            'admin_token' => null,
        ],
    ],
];
