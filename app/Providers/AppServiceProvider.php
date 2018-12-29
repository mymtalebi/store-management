<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() == 'local') {
            $this->app->bind(
                Illuminate\Database\ConnectionResolverInterface::class,
                Illuminate\Database\ConnectionResolver::class);
            $this->app->register(\Niellles\LumenCommands\LumenCommandsServiceProvider::class);
        }
    }
}
