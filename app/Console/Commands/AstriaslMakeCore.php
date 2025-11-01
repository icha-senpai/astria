<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AstriaMakeCore extends Command
{
    protected $signature = 'astria:make-core';
    protected $description = 'Scaffold the Astria Core module (one-time)';

    public function handle(): int
    {
        $base = base_path('modules/Core');
        if (is_dir($base)) {
            $this->warn('Core already exists at modules/Core');
            return self::SUCCESS;
        }

        // Directories
        foreach ([
            "$base/Filament/Pages",
            "$base/Filament/Resources",
            "$base/Providers",
            "$base/routes",
            "$base/resources/views",
            "$base/database/migrations",
            "$base/lang",
        ] as $dir) {
            File::makeDirectory($dir, 0755, true);
            File::put("$dir/.gitkeep", '');
        }

        // module.php
        File::put("$base/module.php", <<<PHP
<?php
return [
    'enabled' => true,
    'providers' => [
        Modules\\Core\\Providers\\CoreServiceProvider::class,
        Modules\\Core\\Filament\\CorePanelProvider::class,
    ],
];
PHP);

        // Providers
        File::put("$base/Providers/CoreServiceProvider.php", <<<PHP
<?php
namespace Modules\\Core\\Providers;

use Illuminate\\Support\\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        \$this->loadViewsFrom(__DIR__.'/../resources/views', 'core');
        \$this->loadTranslationsFrom(__DIR__.'/../lang', 'core');
        \$this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
PHP);

        // Panel Provider
        File::put("$base/Filament/CorePanelProvider.php", <<<PHP
<?php
namespace Modules\\Core\\Filament;

use Filament\\Panel;
use Filament\\PanelProvider;
use Filament\\Pages\\Dashboard;

class CorePanelProvider extends PanelProvider
{
    public function panel(Panel \$panel): Panel
    {
        return \$panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Astria Admin')
            ->discoverResources(in: base_path('modules'), for: 'Modules')
            ->discoverPages(in: base_path('modules'), for: 'Modules')
            ->colors(['primary' => '#00eaff'])
            ->pages([Dashboard::class]);
    }
}
PHP);

        // Test Page + view
        File::put("$base/Filament/Pages/TestCorePage.php", <<<PHP
<?php
namespace Modules\\Core\\Filament\\Pages;

use Filament\\Pages\\Page;

class TestCorePage extends Page
{
    public string \$view = 'core::test-page';

    public static function getNavigationGroup(): ?string { return 'Core'; }
    public static function getNavigationLabel(): string { return 'Test Core Page'; }
}
PHP);

        File::put("$base/resources/views/test-page.blade.php", <<<BLADE
<x-filament-panels::page>
    <div class="text-white text-xl">Core Module Page ✅</div>
</x-filament-panels::page>
BLADE);

        // routes
        File::put("$base/routes/web.php", <<<PHP
<?php
use Illuminate\\Support\\Facades\\Route;
Route::get('/core-ping', fn() => 'Core OK');
PHP);

        $this->info('✅ Core module created at modules/Core');
        $this->line('Visit /admin and /core-ping after cache clear.');
        return self::SUCCESS;
    }
}
