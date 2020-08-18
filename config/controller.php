<?php

return [
    'home' => [
        'middleware' => [
//            '\\App\\Middleware\\Json',
        ],
    ],

    'default' => 'hello world',

    'global_middleware' => [
//        \App\Middleware\Login::class,
        \App\Middleware\Json::class,
        \App\Middleware\Template::class
    ],
];