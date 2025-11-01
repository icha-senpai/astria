<?php

use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;
use Illuminate\Support\Collection;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/_debug/panels', function () {
    return collect(Filament::getPanels())->map(fn ($p) => [
        'id' => $p->getId(),
        'path' => $p->getPath(),
        'class' => get_class($p),
        'brand' => method_exists($p, 'getBrandName') ? $p->getBrandName() : null,
    ]);
});
Route::get('/astria-debug-panels', function () {
    $panels = Filament::getPanels(); // returns array in Filament v4+

    return collect($panels)->map(fn($p) => [
        'id' => $p->getId(),
        'path' => $p->getPath(),
        'class' => get_class($p),
        'brand' => method_exists($p, 'getBrandName') ? $p->getBrandName() : null,
    ])->values();
});