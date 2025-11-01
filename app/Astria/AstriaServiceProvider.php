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
        // Discover modules under /modules
        $modulesDir = Astria::modulesPath();
        if (! is_dir($modulesDir)) {
            return;
        }

        foreach (File::directories($modulesDir) as $dir) {
            $name = basename($dir);
            $configFile = $dir . '/module.php';
            if (! is_file($configFile)) {
                continue;
            }

            /** @var array{name?:string,enabled?:bool,providers?:array,autoload?:array,routes?:array,panels?:array} $cfg */
            $cfg = require $configFile;

            if (! Arr::get($cfg, 'enabled', true)) {
                continue;
            }

            // 1) Register module providers (Laravel & Filament PanelProviders)
            foreach ((array) Arr::get($cfg, 'providers', []) as $providerClass) {
                if (class_exists($providerClass)) {
                    $this->app->register($providerClass);
                }
            }

            // 2) Optional classmap/psr-4 autoloaders (handy during prototyping)
            foreach ((array) Arr::get($cfg, 'autoload.classmap', []) as $path) {
                $this->app->make('files')->requireOnce($dir . '/' . ltrim($path, '/'));
            }

            // 3) Defer routes; we’ll register in boot() to ensure middleware stack is ready
            $this->app->instance("astria.module.$name.config", $cfg);
        }
    }

    public function boot(): void
    {
        // Bind routes for each enabled module (web & api if present)
        $modulesDir = Astria::modulesPath();
        if (! is_dir($modulesDir)) {
            return;
        }

        foreach (File::directories($modulesDir) as $dir) {
            $name = basename($dir);
            /** @var array|null $cfg */
            $cfg = $this->app->bound("astria.module.$name.config")
                ? $this->app->get("astria.module.$name.config")
                : null;

            if (! $cfg) {
                continue;
            }

            // web.php
            $web = $dir . '/routes/web.php';
            if (is_file($web)) {
                Route::middleware('web')->group($web);
            }

            // api.php
            $api = $dir . '/routes/api.php';
            if (is_file($api)) {
                Route::prefix('api')->middleware('api')->group($api);
            }

            // views (resources/views)
            $views = $dir . '/resources/views';
            if (is_dir($views)) {
                $this->loadViewsFrom($views, "modules:$name");
            }

            // migrations (database/migrations)
            $migrations = $dir . '/database/migrations';
            if (is_dir($migrations)) {
                $this->loadMigrationsFrom($migrations);
            }

            // translations (lang)
            $lang = $dir . '/lang';
            if (is_dir($lang)) {
                $this->loadTranslationsFrom($lang, "modules:$name");
            }
        }
    }
}
