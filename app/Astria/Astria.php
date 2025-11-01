<?php

namespace App\Astria;

final class Astria
{
    public static function modulesPath(): string
    {
        return base_path('modules');
    }

    public static function moduleConfigPath(string $name): string
    {
        return static::modulesPath() . '/' . $name . '/module.php';
    }
}
