<?php

/**
 * This file is part of Store Management project.
 *
 * (c) Maryam Talebi <mym.talebi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file readme.md.
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        if ('local' === $this->app->environment()) {
            $this->app->bind(
                Illuminate\Database\ConnectionResolverInterface::class,
                Illuminate\Database\ConnectionResolver::class);
            $this->app->register(\Niellles\LumenCommands\LumenCommandsServiceProvider::class);
        }
    }
}
