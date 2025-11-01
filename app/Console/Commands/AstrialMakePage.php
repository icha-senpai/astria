<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AstriaMakePage extends Command
{
    protected $signature = 'astria:make:page {module} {name}';
    protected $description = 'Create a Filament Page inside a module';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $class = Str::studly($this->argument('name'));
        $ns = "Modules\\{$module}\\Filament\\Pages";
        $dir = base_path("modules/{$module}/Filament/Pages");
        $viewDir = base_path("modules/{$module}/resources/views");
        $viewKey = strtolower($module) . '::' . Str::kebab($class);

        if (! is_dir($dir)) {
            $this->error("Module {$module} not found.");
            return self::FAILURE;
        }

        $path = "{$dir}/{$class}.php";
        if (file_exists($path)) {
            $this->error("Page already exists: {$path}");
            return self::FAILURE;
        }

        File::put($path, <<<PHP
<?php
namespace {$ns};

use Filament\\Pages\\Page;

class {$class} extends Page
{
    public string \$view = '{$viewKey}';

    public static function getNavigationGroup(): ?string { return '{$module}'; }
    public static function getNavigationLabel(): string { return '{$class}'; }
}
PHP);

        if (! is_dir($viewDir)) File::makeDirectory($viewDir, 0755, true);
        File::put("{$viewDir}/" . Str::kebab($class) . ".blade.php", <<<BLADE
<x-filament-panels::page>
    <div class="text-white text-xl">{$module} / {$class} ✅</div>
</x-filament-panels::page>
BLADE);

        $this->info("✅ Page {$ns}\\{$class} created.");
        return self::SUCCESS;
    }
}
