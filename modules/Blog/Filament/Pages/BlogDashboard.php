<?php

namespace Modules\Blog\Filament\Pages;

use Filament\Pages\Page;

class BlogDashboard extends Page
{
    public string $view = 'blog::dashboard';

    public static function getNavigationGroup(): ?string
    {
        return 'Blog';
    }

    public static function getNavigationLabel(): string
    {
        return 'Blog Dashboard';
    }
}
