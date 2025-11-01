<?php

namespace Modules\Test\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class TestServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (File::exists(__DIR__.'/../Routes/web.php')) {
            $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        }

        if (File::exists(__DIR__.'/../Database/migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
        }

        if (File::exists(__DIR__.'/../Resources/views')) {
            $this->loadViewsFrom(__DIR__.'/../Resources/views', strtolower('Test'));
        }
    }
}