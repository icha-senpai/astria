<?php

namespace App\Providers;

use Filament\Panel;
use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class AdminPanelProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Do nothing — we override via Astria provider.
    }

    public function register(): void
    {
        // Kill default panel
        Filament::serving(function () {
            Filament::panel('all', function (Panel $panel) {
                return $panel->disabled(); // disable default Filament panel
            });
        });
    }
}
