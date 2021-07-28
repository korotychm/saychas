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
use Laminas\Session\Container;// as SessionContainer;
use Laminas\Session\Storage\SessionArrayStorage;
//use Laminas\Session\Config\SessionConfig;
//use Laminas\Session\Validator\RemoteAddr;
//use Laminas\Session\Validator\HttpUserAgent;
//use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
//use Laminas\Session;
//echo __DIR__ . '/../src/Entity';
//exit;
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
//                'dsn'    => 'mysql:dbname=test_z;host=localhost;charset=utf8',
                'dsn'    => 'mysql:dbname=saychas_z;host=localhost;charset=utf8',
                'driver_options' => [
                    PDO::MYSQL_ATTR_INIT_COMMAND =>'SET NAMES \'UTF8\'',
                ],
                'username' => 'saychas_z',// 'test_z',
                'password' => 'saychas_z',// 'test_z',
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
//    'session_containers' => [
//        //Laminas\Session\Container::class,
//        Container::class,
//    ],
    'session_storage' => [
        'type' => SessionArrayStorage::class,
    ],
    'session_config'  => [
        // Session data will be stored on server maximum for 30 days.
        'gc_maxlifetime' => 60*60*24*30,
        // Session cookie will expire in 1 hour.
        'cookie_lifetime' => 60*60*24*30,// 60*60*1,
        //'remember_me_seconds' => 1,// 60*60*24*30,
        // …
        'cache_expire' => 60*60*24*30,// 60*24,
//        'options' => [
//            //'remember_me_seconds' => 1,
//            'name' => 'laminas-shlaminas',
//        ],
    ],
/** We will use the session_manager later on */
//    'session_manager' => [
//        'config' => [
//            'class' => SessionConfig::class,
//            'options' => [
//                'name' => 'saychas',
//            ],
//        ],
//        'storage' => SessionArrayStorage::class,
//        'validators' => [
//            RemoteAddr::class,
//            HttpUserAgent::class,
//        ],
//    ],
    // Cache configuration.
    'caches' => [
        'FilesystemCache' => [
            'adapter' => [
                'name'    => \Laminas\Cache\Storage\Adapter\Filesystem::class,
                'options' => [
                    // Store cached data in this directory.
                    'cache_dir' => './data/cache',
                    // Store cached data for 1 hour.
                    'ttl' => 60*60*24*30, // 60*60*1
                ],
            ],
            'plugins' => [
                [
                    'name' => 'serializer',
                    'options' => [                        
                    ],
                ],
            ],
        ],
    ],

//    'laminas-cli' => [
//        'commands' => [
//            'package:fetch-images' => \Application\Command\FetchImagesCommand::class,
//        ],
//    ],
//    'service_manager' => [
//        'factories' => [
//            \Application\Command\FetchImagesCommand::class => \Application\Command\Factory\FetchImagesCommandFactory::class,
//        ],
//    ],

//    'dependencies' => [
//        'factories' => [
//            \Application\Command\FetchImagesCommand::class => \Application\Command\Factory\FetchImagesCommandFactory::class,
//        ],
//    ],
    
//    'doctrine' => [        
//        // migrations configuration
////        'migrations_configuration' => [
////            'orm_default' => [
////                'directory' => 'data/Migrations',
////                'name'      => 'Doctrine Database Migrations',
////                'namespace' => 'Migrations',
////                'table'     => 'migrations',
////            ],
////        ],
//        
//        'driver' => [
//            __NAMESPACE__ . '_driver' => [
//                'class' => AnnotationDriver::class,
//                'cache' => 'array',
//                'paths' => [__DIR__ . '/../../module/Application/src/Entity']
//            ],
//            'orm_default' => [
//                'drivers' => [
//                    __NAMESPACE__ . '\Application\Entity' => __NAMESPACE__ . '_driver'
//                ],
//            ],
//        ],
////        'driver' => [
////            // defines an annotation driver with two paths, and names it `my_annotation_driver`
////            'my_annotation_driver' => [
////                'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
////                'cache' => 'array',
////                'paths' => [
////                    'module/Entity',
////                ],
////            ],
////
////            // default metadata driver, aggregates all other drivers into a single one.
////            // Override `orm_default` only if you know what you're doing
////            'orm_default' => [
////                'drivers' => [
////                    // register `my_annotation_driver` for any entity under namespace `My\Namespace`
////                    'My\Namespace' => 'my_annotation_driver',
////                ],
////            ],
////        ],
//        'connection' => [
//            // default connection name
//            'orm_default' => [
//                'driverClass' => \Doctrine\DBAL\Driver\PDOMySql\Driver::class,
//                'params' => [
//                    'host'     => 'localhost',
//                    'port'     => '3306',
//                    'user'     => 'saychas_z',
//                    'password' => 'saychas_z',
//                    'dbname'   => 'saychas_z',
//                ],
//            ],
//        ],
//        'directory' => 'data/DoctrineORMModule/Proxy',
//    ],
        
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
