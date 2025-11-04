<?php
// Astria - Personal Digital OS
// Copyright (C) 2025 Icha Senpai
// Licensed under AGPLv3. See LICENSE for details.
return [
    'name'    => 'core',
    'version' => '0.0.1',
    'enabled' => true,

    'providers' => [
        Modules\Core\Providers\CoreServiceProvider::class,
        Modules\Core\Filament\CorePanelProvider::class, // <- PanelProvider
    ],
];
