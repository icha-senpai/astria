<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AstriaDiagnose extends Command
{
    protected $signature = 'astria:diagnose';
    protected $description = 'Diagnose Astria modules and panel wiring';

    public function handle(): int
    {
        $modulesDir = base_path('modules');
        if (! is_dir($modulesDir)) {
            $this->error('No modules/ directory found.');
            return self::FAILURE;
        }

        $found = 0;
        foreach (File::directories($modulesDir) as $dir) {
            $name = basename($dir);
            $cfgFile = $dir . '/module.php';
            if (! is_file($cfgFile)) {
                $this->warn("⚠ {$name}: missing module.php");
                continue;
            }
            $cfg = require $cfgFile;
            $enabled = $cfg['enabled'] ?? true;
            $this->line(($enabled ? '✅' : '⏸') . " {$name}");

            $views = is_dir("$dir/resources/views") ? 'views' : '';
            $routes = (is_file("$dir/routes/web.php") ? 'web' : '') . (is_file("$dir/routes/api.php") ? '/api' : '');
            $migs  = is_dir("$dir/database/migrations") ? 'migrations' : '';
            $langs = is_dir("$dir/lang") ? 'lang' : '';
            $this->line("    paths: {$routes} {$views} {$migs} {$langs}");
            $found++;
        }

        if ($found === 0) $this->warn('No modules found.');
        $this->info('Done.');
        return self::SUCCESS;
    }
}
