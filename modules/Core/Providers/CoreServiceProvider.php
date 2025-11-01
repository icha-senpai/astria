<?php

namespace Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class CoreServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Auto-load routes if exists
        if (File::exists($routes = __DIR__ . '/../Routes/web.php')) {
            $this->loadRoutesFrom($routes);
        }

        // Auto-load migrations if exists
        if (File::exists($migrations = __DIR__ . '/../Database/migrations')) {
            $this->loadMigrationsFrom($migrations);
        }

        // Auto-load views if exists
        if (File::exists($views = __DIR__ . '/../Resources/views')) {
            $this->loadViewsFrom($views, 'core');
        }
    }
}
