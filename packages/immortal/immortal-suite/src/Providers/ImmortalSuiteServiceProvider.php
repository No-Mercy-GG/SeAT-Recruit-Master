<?php

namespace Immortal\Suite\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Immortal\Suite\Http\Middleware\FeatureFlag;
use Immortal\Suite\Http\Middleware\ImmortalApiAdminToken;
use Immortal\Suite\Http\Middleware\ImmortalApiSignature;
use Immortal\Suite\Services\SettingService;

class ImmortalSuiteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/immortal-suite.php', 'immortal-suite');
        $this->app->singleton(SettingService::class, function ($app) {
            return new SettingService();
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'immortal-suite');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->publishes([
            __DIR__ . '/../../config/immortal-suite.php' => config_path('immortal-suite.php'),
        ], 'immortal-suite-config');

        $this->registerPermissions();
        $this->registerMiddleware();
    }

    private function registerPermissions(): void
    {
        $permissions = [
            'view_applications',
            'manage_applications',
            'view_dossier',
            'manage_risk',
            'manage_settings',
            'view_audit',
            'view_intel',
            'manage_discord',
        ];

        foreach ($permissions as $permission) {
            Gate::define('immortal.' . $permission, function ($user) use ($permission) {
                return method_exists($user, 'hasPermission')
                    ? $user->hasPermission('immortal.' . $permission)
                    : false;
            });
        }
    }

    private function registerMiddleware(): void
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('immortal.feature', FeatureFlag::class);
        $router->aliasMiddleware('immortal.signature', ImmortalApiSignature::class);
        $router->aliasMiddleware('immortal.admin', ImmortalApiAdminToken::class);
    }
}
