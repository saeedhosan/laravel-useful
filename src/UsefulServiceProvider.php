<?php

declare(strict_types=1);

namespace SaeedHosan\Useful;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class UsefulServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishStubs();
        $this->registerCommands();
    }

    private function publishStubs(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/Console/Commands/stubs' => base_path('stubs'),
        ], 'useful-stubs');
    }

    private function registerCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Console\Commands\ActionMakeCommand::class,
        ]);
    }
}
