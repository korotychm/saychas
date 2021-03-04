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

use Laminas\Db\Adapter;

return [
    'service_manager' => [
        'abstract_factories' => [
            Adapter\AdapterAbstractServiceFactory::class,
        ],
        'factories' => [
            Adapter\AdapterInterface::class => Adapter\AdapterServiceFactory::class,
        ],
        'aliases' => [
            Adapter\Adapter::class => Adapter\AdapterInterface::class
        ]
    ],
    'db' => [
        'adapters' => [
            'Application\Db\WriteAdapter' => [
                'driver' => 'Pdo_Mysql',
                'dsn'    => 'mysql:dbname=test_z;host=localhost;charset=utf8',
                'driver_options' => [
                    PDO::MYSQL_ATTR_INIT_COMMAND =>'SET NAMES \'UTF8\'',
                ],
                'username' => 'test_z',
                'password' => 'test_z',
            ],
            'Application\Db\ReadOnlyAdapter' => [
                'driver' => 'Pdo_Mysql',
                'dsn'    => 'mysql:dbname=saychas_z;host=localhost;charset=utf8',
            ],
        ],
        'driver' => 'Pdo_Mysql',
        'dsn' => 'mysql:dbname=saychas_z;hostname=localhost',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND =>'SET NAMES \'UTF8\'',
        ],
    ],
    'doctrine' => [        
        // migrations configuration
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => 'data/Migrations',
                'name'      => 'Doctrine Database Migrations',
                'namespace' => 'Migrations',
                'table'     => 'migrations',
            ],
        ],
    ],
];

//return [
//    ['adapters' => [
//        'Application\Db\ReadOnly' => [
//            'driver'   => 'Pdo_Sqlite',
//            'database' => 'data/db/users.db',
//        ],
//        'Application\Db\WriteAdapter' => [
////        'default_db' => [
//            'driver'   => 'Pdo_Mysql',
//            'database' => 'saychas_z',
//            'username' => 'saychas_z',
//            'password' => 'saychas_z',
//        ],
//    ]],
//    
//];
