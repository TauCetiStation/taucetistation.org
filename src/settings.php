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
            'gamma' => [
                'name' => 'Gamma Station',
                'desciption' => 'gamma',
                'address' => '5.9.12.156',
                'port' => '2507',
            ],
            'alien' => [
                'name' => 'Colonial Marines',
                'desciption' => 'alien',
                'address' => '5.9.12.156',
                'port' => '2495',
            ],
            'fallout' => [
                'name' => 'Fallout',
                'desciption' => 'Пустоши',
                'address' => '5.9.12.156',
                'port' => '2506',
                'hub' => true,
                'hubShowDefault' => true,
                'hubSidetag' => true,
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
