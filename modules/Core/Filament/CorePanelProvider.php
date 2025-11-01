<?php

namespace Modules\Core\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;

class CorePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Astria Admin')

            // 👇 discover ALL module resources & pages
            ->discoverResources(
                in: base_path('modules'),
                for: 'Modules'
            )
            ->discoverPages(
                in: base_path('modules'),
                for: 'Modules'
            )

            ->pages([
                Dashboard::class,
            ]);
    }
}
