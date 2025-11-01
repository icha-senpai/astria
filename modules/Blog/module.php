<?php

return [
    'name'    => 'blog',
    'version' => '0.0.1',
    'enabled' => true,

    'providers' => [
        Modules\Blog\Providers\BlogServiceProvider::class,
    ],
];