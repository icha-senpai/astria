<?php

namespace App\Astria;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Filament\Facades\Filament;
use Filament\PanelProvider;

class AstriaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        logger("🔥 Astria booting modular panels");

        $modulesPath = Astria::modulesPath();

        foreach (File::directories($modulesPath) as $moduleDir) {
            $manifest = $moduleDir . '/module.php';

            if (! File::exists($manifest)) {
                continue;
            }

            $config = require $manifest;

            if (($config['enabled'] ?? true) === false) {
                continue;
            }

            // ✅ Step 1: register module providers
            foreach ($config['providers'] ?? [] as $provider) {
                $this->app->register($provider);
            }

            // ✅ Step 2: register Filament panels
            $filamentPath = $moduleDir . '/Filament';

            if (File::isDirectory($filamentPath)) {
                foreach (File::allFiles($filamentPath) as $file) {
                    $class = $this->fileToNamespace($file->getRealPath());

                    if (! class_exists($class)) {
                        continue;
                    }

                    // ✅ Only register PanelProviders
                    if (! is_subclass_of($class, PanelProvider::class)) {
                        continue;
                    }

                    Filament::serving(function () use ($class) {
                        logger("✅ Astria registering panel provider: $class");
                        Filament::registerPanelProvider(new $class());
                    });
                }
            }
        }
    }

    protected function fileToNamespace($path)
    {
        $path = str_replace('\\', '/', $path);
        $base = str_replace('\\', '/', base_path() . '/modules/');

        $relative = str_replace($base, '', $path);
        $relative = str_replace('.php', '', $relative);
        $relative = str_replace('/', '\\', $relative);

        return "Modules\\{$relative}";
    }
}
