<?php

namespace DodoPayments\Laravel;

use Illuminate\Support\ServiceProvider;

class DodoPaymentsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/dodopayments.php', 'dodopayments'
        );

        // Register the main class to use with the facade
        $this->app->singleton('dodopayments', function ($app) {
            return new DodoPayments();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/dodopayments.php' => config_path('dodopayments.php'),
        ], 'dodopayments-config');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/dodopayments'),
        ], 'dodopayments-views');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'dodopayments');

        // Load routes
        if (config('dodopayments.routes.enabled', true)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        // Register commands if running in console
        if ($this->app->runningInConsole()) {
            $this->commands([
                // Future: Add Artisan commands here
            ]);
        }
    }
}
