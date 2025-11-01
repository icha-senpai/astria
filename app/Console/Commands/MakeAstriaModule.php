<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeAstriaModule extends Command
{
    protected $signature = 'astria:module {name}';
    protected $description = 'Create a new Astria module';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $slug = Str::kebab($name);
        $base = base_path("modules/{$name}");

        if (is_dir($base)) {
            $this->error("Module {$name} already exists.");
            return self::FAILURE;
        }

        $dirs = [
            "{$base}/routes",
            "{$base}/resources/views",
            "{$base}/database/migrations",
            "{$base}/Filament/Pages",
            "{$base}/Filament/Resources",
            "{$base}/Providers",
        ];
        foreach ($dirs as $dir) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put("{$base}/module.php", <<<PHP
<?php

return [
    'name'    => '{$slug}',
    'version' => '0.0.1',
    'enabled' => true,

    'providers' => [
        Modules\\{$name}\\Providers\\{$name}ServiceProvider::class,
        Modules\\{$name}\\Filament\\{$name}PanelProvider::class,
    ],
];
PHP);

        File::put("{$base}/routes/web.php", <<<PHP
<?php

use Illuminate\\Support\\Facades\\Route;

Route::get('/{$slug}-ping', fn() => '{$name} module online');
PHP);

        File::put("{$base}/Providers/{$name}ServiceProvider.php", <<<PHP
<?php

namespace Modules\\{$name}\\Providers;

use Illuminate\\Support\\ServiceProvider;

class {$name}ServiceProvider extends ServiceProvider
{
    public function register(): void {}
    public function boot(): void {}
}
PHP);

        File::put("{$base}/Filament/{$name}PanelProvider.php", <<<PHP
<?php

namespace Modules\\{$name}\\Filament;

use Filament\\Panel;
use Filament\\PanelProvider;
use Filament\\Pages\\Dashboard;

class {$name}PanelProvider extends PanelProvider
{
    public function panel(Panel \$panel): Panel
    {
        return \$panel
            ->id('{$slug}')
            ->path('/{$slug}/admin')
            ->login()
            ->brandName('Astria {$name}')
            ->colors([
                'primary' => '#00eaff',
            ])
            ->discoverResources(
                in: __DIR__.'/Resources',
            )
            ->discoverPages(
                in: __DIR__.'/Pages',
            )
            ->pages([
                Dashboard::class,
            ]);
    }
}
PHP);

        $this->info("✅ Module {$name} created at modules/{$name}");
        return self::SUCCESS;
    }
}
