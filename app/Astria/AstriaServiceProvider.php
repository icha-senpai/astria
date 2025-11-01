<?php

namespace App\Astria;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AstriaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $modulesDir = base_path('modules');
        if (! is_dir($modulesDir)) return;

        foreach (File::directories($modulesDir) as $dir) {
            $name = basename($dir);
            $configFile = $dir . '/module.php';
            if (! is_file($configFile)) continue;

            /** @var array $cfg */
            $cfg = require $configFile;
            if (! Arr::get($cfg, 'enabled', true)) continue;

            // Register module-local providers (e.g., BlogServiceProvider)
            foreach ((array) Arr::get($cfg, 'providers', []) as $providerClass) {
                if (class_exists($providerClass)) {
                    $this->app->register($providerClass);
                }
            }

            // Stash cfg so boot() can wire routes/views/etc
            $this->app->instance("astria.module.$name.config", $cfg);
            $this->app->instance("astria.module.$name.path", $dir);
        }
    }

    public function boot(): void
    {
        $modulesDir = base_path('modules');
        if (! is_dir($modulesDir)) return;

        foreach (File::directories($modulesDir) as $dir) {
            $name = basename($dir);
            $cfg = $this->app->bound("astria.module.$name.config")
                ? $this->app->get("astria.module.$name.config")
                : null;
            if (! $cfg) continue;

            // Routes
            $web = $dir . '/routes/web.php';
            if (is_file($web)) {
                Route::middleware('web')->group($web);
            }
            $api = $dir . '/routes/api.php';
            if (is_file($api)) {
                Route::prefix('api')->middleware('api')->group($api);
            }

            // Views (namespace = lowercased module name)
            $views = $dir . '/resources/views';
            if (is_dir($views)) {
                $this->loadViewsFrom($views, strtolower($name));
            }

            // Lang
            $lang = $dir . '/lang';
            if (is_dir($lang)) {
                $this->loadTranslationsFrom($lang, strtolower($name));
            }

            // Migrations
            $migrations = $dir . '/database/migrations';
            if (is_dir($migrations)) {
                $this->loadMigrationsFrom($migrations);
            }
        }
    }
}

