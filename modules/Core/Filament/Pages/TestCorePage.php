<?php

namespace Modules\Core\Filament\Pages;

use Filament\Pages\Page;

class TestCorePage extends Page
{
    public string $view = 'core::test-page';

    public static function getNavigationLabel(): string
    {
        return 'Test Core Page';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Core';
    }
}
