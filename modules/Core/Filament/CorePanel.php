<?php

namespace Modules\Core\Filament;

use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;

class CorePanel extends PanelProvider
{
    public static string $panelId = 'core';
    public static string $path = 'admin';

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('core')
            ->path('admin')
            ->login()
            ->brandName('Astria Modular CMS')
            ->colors([
                'primary' => '#00ffff',
            ])
            ->discoverResources(
                in: __DIR__.'/Resources',
                for: 'Modules\\Core\\Filament\\Resources'
            )
            ->discoverPages(
                in: __DIR__.'/Pages',
                for: 'Modules\\Core\\Filament\\Pages'
            )
            ->pages([
                Dashboard::class,
            ]);
    }
}
