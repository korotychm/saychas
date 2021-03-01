<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return [
    ['adapters' => [
        'Application\Db\ReadOnly' => [
            'driver'   => 'Pdo_Sqlite',
            'database' => 'data/db/users.db',
        ],
        'Application\Db\WriteAdapter' => [
//        'default_db' => [
            'driver'   => 'Pdo_Mysql',
            'database' => 'saychas_z',
            'username' => 'saychas_z',
            'password' => 'saychas_z',
        ],
    ]],
    
];
