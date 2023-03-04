<?php

namespace Webdevsavvy\LaravelViewCommand;

use Illuminate\Support\ServiceProvider;

class LaravelViewCommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeViewCommand::class
            ]);
        }
    }

    public function register()
    {

    }
}