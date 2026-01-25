<?php

use Illuminate\Support\Facades\Route;
use Immortal\Suite\Http\Controllers\Api\ApplicantsController;
use Immortal\Suite\Http\Controllers\Api\IntelController;
use Immortal\Suite\Http\Controllers\Api\RiskController;
use Immortal\Suite\Http\Middleware\ImmortalApiAdminToken;
use Immortal\Suite\Http\Middleware\ImmortalApiSignature;
use Immortal\Suite\Http\Middleware\ImmortalApiToken;

Route::middleware(['api', ImmortalApiToken::class, ImmortalApiSignature::class])->prefix('api/v1/immortal')->group(function () {
    Route::get('/applicants', [ApplicantsController::class, 'index']);
    Route::get('/applicants/{application}', [ApplicantsController::class, 'show']);
    Route::get('/applicants/{application}/risk', [RiskController::class, 'show']);
    Route::get('/applicants/{application}/compliance', [ApplicantsController::class, 'compliance']);
    Route::get('/intel/recent', [IntelController::class, 'recent']);

    Route::middleware([ImmortalApiAdminToken::class])->group(function () {
        Route::post('/applicants/{application}/claim', [ApplicantsController::class, 'claim']);
        Route::post('/applicants/{application}/status', [ApplicantsController::class, 'setStatus']);
        Route::post('/applicants/{application}/notes', [ApplicantsController::class, 'addNote']);
        Route::post('/intel/record', [IntelController::class, 'record']);
    });
});
