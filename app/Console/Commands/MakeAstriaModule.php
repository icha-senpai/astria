<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeAstriaModule extends Command
{
    protected $signature = 'astria:module {name}';
    protected $description = 'Create a new Astria module';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $modulePath = base_path("modules/{$name}");

        if (File::exists($modulePath)) {
            $this->error("Module {$name} already exists!");
            return;
        }

        // Create folders
        File::makeDirectory($modulePath);
        File::makeDirectory("{$modulePath}/Routes", 0755, true);
        File::makeDirectory("{$modulePath}/Database/migrations", 0755, true);
        File::makeDirectory("{$modulePath}/Resources/views", 0755, true);
        File::makeDirectory("{$modulePath}/Providers", 0755, true);

        // Manifest
        $manifest = <<<PHP
<?php

return [
    'name' => '{$name}',
    'version' => '0.0.1',
    'enabled' => true,
    'providers' => [
        Modules\\{$name}\\Providers\\{$name}ServiceProvider::class,
    ],
];
PHP;

        File::put("{$modulePath}/module.php", $manifest);

        // Provider stub (NOWDOC template)
        $providerStub = <<<'PHP'
<?php

namespace Modules\__NAME__\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class __NAME__ServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $modulePath = __DIR__ . '/../';

        if (File::exists($modulePath . 'Routes/web.php')) {
            $this->loadRoutesFrom($modulePath . 'Routes/web.php');
        }

        if (File::exists($modulePath . 'Database/migrations')) {
            $this->loadMigrationsFrom($modulePath . 'Database/migrations');
        }

        if (File::exists($modulePath . 'Resources/views')) {
            $this->loadViewsFrom($modulePath . 'Resources/views', strtolower('__NAME__'));
        }
    }
}
PHP;

        $provider = str_replace('__NAME__', $name, $providerStub);

        File::put("{$modulePath}/Providers/{$name}ServiceProvider.php", $provider);

        $this->info("Module {$name} created successfully.");
    }
}
