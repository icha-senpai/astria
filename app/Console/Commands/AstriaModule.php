<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AstriaModule extends Command
{
    protected $signature = 'astria:module {name}';
    protected $description = 'Create a new Astria plug-in module';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $slug = Str::kebab($name);
        $base = base_path("modules/{$name}");

        if (is_dir($base)) {
            $this->error("Module {$name} already exists.");
            return self::FAILURE;
        }

        foreach ([
            "$base/Providers",
            "$base/routes",
            "$base/resources/views",
            "$base/Filament/Pages",
            "$base/Filament/Resources",
            "$base/database/migrations",
            "$base/lang",
        ] as $dir) {
            File::makeDirectory($dir, 0755, true);
            File::put("$dir/.gitkeep", '');
        }

        File::put("$base/module.php", <<<PHP
<?php
return [
    'enabled' => true,
    'providers' => [
        Modules\\{$name}\\Providers\\{$name}ServiceProvider::class,
    ],
];
PHP);

        File::put("$base/Providers/{$name}ServiceProvider.php", <<<PHP
<?php
namespace Modules\\{$name}\\Providers;

use Illuminate\\Support\\ServiceProvider;

class {$name}ServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        \$this->loadViewsFrom(__DIR__.'/../resources/views', strtolower('{$name}'));
        \$this->loadTranslationsFrom(__DIR__.'/../lang', strtolower('{$name}'));
        \$this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
PHP);

        File::put("$base/routes/web.php", <<<PHP
<?php
use Illuminate\\Support\\Facades\\Route;
Route::get('/{$slug}-ping', fn() => '{$name} module online');
PHP);

        $this->info("✅ Module {$name} created at modules/{$name}");
        $this->line("This is a plug-in module. It auto-integrates into /admin.");
        return self::SUCCESS;
    }
}
