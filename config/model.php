<?php

return [
    'contact_information' => [
        'observer' => [],
    ],
    'global_observer' => [
        \App\Observer\WriteCreatedObserver::class,
        \App\Observer\WriteUpdatedObserver::class,
    ],
];