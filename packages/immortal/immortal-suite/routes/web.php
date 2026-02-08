<?php

use Illuminate\Support\Facades\Route;
use Immortal\Suite\Http\Controllers\ApplicationsController;
use Immortal\Suite\Http\Controllers\AuditLogController;
use Immortal\Suite\Http\Controllers\ComplianceController;
use Immortal\Suite\Http\Controllers\DashboardController;
use Immortal\Suite\Http\Controllers\DiscordController;
use Immortal\Suite\Http\Controllers\DossierController;
use Immortal\Suite\Http\Controllers\IntelController;
use Immortal\Suite\Http\Controllers\RiskEngineController;
use Immortal\Suite\Http\Controllers\SettingsController;

Route::middleware(['web', 'auth'])->prefix('immortal')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('immortal.feature:applications')
        ->middleware('can:immortal.view_applications')
        ->name('immortal.dashboard');

    Route::get('/applications', [ApplicationsController::class, 'index'])
        ->middleware('immortal.feature:applications')
        ->middleware('can:immortal.view_applications')
        ->name('immortal.applications');
    Route::get('/applications/{application}', [ApplicationsController::class, 'show'])
        ->middleware('immortal.feature:applications')
        ->middleware('can:immortal.view_applications')
        ->name('immortal.applications.show');
    Route::post('/applications/{application}/status', [ApplicationsController::class, 'updateStatus'])
        ->middleware('immortal.feature:applications')
        ->middleware('can:immortal.manage_applications')
        ->name('immortal.applications.status');
    Route::post('/applications/{application}/claim', [ApplicationsController::class, 'claim'])
        ->middleware('immortal.feature:applications')
        ->middleware('can:immortal.manage_applications')
        ->name('immortal.applications.claim');
    Route::post('/applications/{application}/notes', [ApplicationsController::class, 'addNote'])
        ->middleware('immortal.feature:applications')
        ->middleware('can:immortal.manage_applications')
        ->name('immortal.applications.notes');

    Route::get('/dossier/{application}', [DossierController::class, 'show'])
        ->middleware('immortal.feature:dossier')
        ->middleware('can:immortal.view_dossier')
        ->name('immortal.dossier');

    Route::get('/risk', [RiskEngineController::class, 'index'])
        ->middleware('immortal.feature:risk_engine')
        ->middleware('can:immortal.manage_risk')
        ->name('immortal.risk');
    Route::post('/risk/{rule}', [RiskEngineController::class, 'update'])
        ->middleware('immortal.feature:risk_engine')
        ->middleware('can:immortal.manage_risk')
        ->name('immortal.risk.update');

    Route::get('/discord', [DiscordController::class, 'index'])
        ->middleware('immortal.feature:discord')
        ->middleware('can:immortal.manage_discord')
        ->name('immortal.discord');

    Route::get('/compliance', [ComplianceController::class, 'index'])
        ->middleware('immortal.feature:compliance')
        ->middleware('can:immortal.view_intel')
        ->name('immortal.compliance');
    Route::get('/intel', [IntelController::class, 'index'])
        ->middleware('immortal.feature:intel')
        ->middleware('can:immortal.view_intel')
        ->name('immortal.intel');

    Route::get('/audit', [AuditLogController::class, 'index'])
        ->middleware('can:immortal.view_audit')
        ->name('immortal.audit');

    Route::get('/settings', [SettingsController::class, 'index'])
        ->middleware('can:immortal.manage_settings')
        ->name('immortal.settings');
    Route::post('/settings', [SettingsController::class, 'update'])
        ->middleware('can:immortal.manage_settings')
        ->name('immortal.settings.update');

    Route::get('/apply/start', [ApplicationsController::class, 'start'])
        ->middleware('immortal.feature:applications')
        ->withoutMiddleware('auth')
        ->name('immortal.apply.start');
    Route::post('/apply/{application}/done', [ApplicationsController::class, 'complete'])
        ->middleware('immortal.feature:applications')
        ->middleware('can:immortal.view_applications')
        ->name('immortal.apply.done');
});
