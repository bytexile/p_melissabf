<?php

return [
    /* Options (mysql, sqlite) */
    'driver' => 'mysql',

    'sqlite' => [
        // 'database' => 'format.db'
        'database' => 'melissa_bf.db'
    ],
    
    'mysql' => [
        'host'      => 'planum.io',
        'database'  => 'planum_melissa',
        'user'      => 'planum_melissa',
        'pass'      => '$HVSU,3;mvR]',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ]
    /*
    'mysql' => [
        'host'      => '127.0.0.1',
        'database'  => 'melissa_blackfriday',
        'user'      => 'root',
        'pass'      => 'vertrigo',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ]
    */
];
