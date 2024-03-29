<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'view' => [
            'template_path' => __DIR__ . '/../templates/',
            'cache' => __DIR__ . '/../cache/',
        ],

        'servers' => [
            /*
            'tauceti' => [ //api path
                'name' => 'Tau Ceti Station',
                'desciption' => 'Основной сервер сообщества',
                'address' => 'game.tauceti.ru',
                'port' => '2506',
                'hub' => false, //add to hub or not (maybe for api-only servers)
                'hubShowAlways' => false, //for main servers
                'hubShowDefault' => false,
                'hubSidetag' => false, //side servers have an additional "side server" tag
            ],
            */
            'tauceti' => [
                'name' => 'Tau Ceti Classic',
                'desciption' => 'Основной сервер сообщества',
                'address' => 'game.taucetistation.org',
                'port' => '2506',
                'hub' => true,
                'hubShowAlways' => true,
            ],
            'tauceti2' => [
                'name' => 'Tau Ceti Classic II',
                'desciption' => 'Основной сервер сообщества',
                'address' => 'game.taucetistation.org',
                'port' => '2507',
                'hub' => true,
                'hubShowAlways' => true,
            ],
            'tauceti3' => [
                'name' => 'Tau Ceti Classic III',
                'desciption' => 'Основной сервер сообщества',
                'address' => 'game.taucetistation.org',
                'port' => '2508',
                'hub' => false,
                'hubShowAlways' => false,
            ],
            'sandbox' => [
                'name' => 'Tau Ceti Sandbox',
                'desciption' => 'Не игровой сервер-песочница',
                'address' => 'game.taucetistation.org',
                'port' => '2510',
                'hub' => true,
                'hubShowAlways' => true,
            ],
            'tg' => [
                'name' => '/tg/station Bagil',
                'desciption' => 'test',
                'address' => 'bagil.tgstation13.org',
                'port' => '2337',
            ],
            'bay' => [
                'name' => 'Baystation12',
                'desciption' => 'test',
                'address' => 'play.baystation12.net',
                'port' => '8000',
            ],
        ],
    ],
];
