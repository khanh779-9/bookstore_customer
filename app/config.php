<?php
return [
    // 'db' => [
    //     'host' => '127.0.0.1',
    //     'dbname' => 'db_nhasach',
    //     'user' => 'root',
    //     'pass' => '',
    //     'charset' => 'utf8mb4',
    // ],
    // Production example (place credentials in env vars / secrets manager, not in source)
//    'db' => [
//         'host' => 'sql309.infinityfree.com',
//         'dbname' => 'if0_40669344_db_nhasach',
//         'user' => 'if0_40669344',
//         'pass' => 'VX8fKDvYtSa',
//         'charset' => 'utf8mb4',
//     ],
     'db' => [
        'host' => 'ballast.proxy.rlwy.net:46056',
        'dbname' => 'db_nhasach',
        'user' => 'root',
        'pass' => 'EAWCmeStFTZmgsBTBSgZuFWeRyaiZnfL',
        'charset' => 'utf8mb4',
    ],
    'base_url' => '/', 
    'api_key' => null, 
    'api_rate_limit' => [ 
        'requests' => 120,
        'window' => 60
    ], 
    'api_allowed_origins' => '*',
];
