<?php

declare(strict_types=1);

namespace App\Providers;

use Laravel\Scout\Console\FlushCommand;
use Laravel\Scout\Console\ImportCommand;
use Laravel\Scout\EngineManager;
use Laravel\Scout\ScoutServiceProvider as ServiceProvider;

class ScoutServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
        $this->app->configure('scout');
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->singleton(EngineManager::class, function ($app): \Laravel\Scout\EngineManager {
            return new EngineManager($app);
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportCommand::class,
                FlushCommand::class,
            ]);
        }
    }
}
