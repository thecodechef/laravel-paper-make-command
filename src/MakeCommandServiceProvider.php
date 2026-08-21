<?php

namespace LaravelPaper\MakeCommand;

use Illuminate\Support\ServiceProvider;
use LaravelPaper\MakeCommand\Console\Commands\MakeCommand;

class MakeCommandServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../stubs' => $this->app->basePath('stubs/laravel-paper-make-command'),
        ], ['laravel-paper-make-command', 'laravel-paper-make-command-stubs']);

        $this->commands([
            MakeCommand::class,
        ]);
    }
}
