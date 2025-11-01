<?php

return [
    'name'    => 'core',
    'version' => '0.0.1',
    'enabled' => true,

    'providers' => [
        Modules\Core\Providers\CoreServiceProvider::class,
        Modules\Core\Filament\CorePanelProvider::class, // <- PanelProvider
        Modules\Blog\Providers\BlogServiceProvider::class,
    ],
];
