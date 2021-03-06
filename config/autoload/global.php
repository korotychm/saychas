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
return [
    'service_manager' => [
        'abstract_factories' => [
            Adapter\AdapterAbstractServiceFactory::class,
        ],
        'factories' => [
            Adapter\AdapterInterface::class => Adapter\AdapterServiceFactory::class,
            // Configures the default SessionManager instance
            //'Laminas\Session\ManagerInterface'
            \Laminas\Session\ManagerInterface::class => \Laminas\Session\Service\SessionManagerFactory::class,// 'Laminas\Session\Service\SessionManagerFactory',
            // Provides session configuration to SessionManagerFactory
            //'Laminas\Session\Config\ConfigInterface'
            \Laminas\Session\Config\ConfigInterface::class => \Laminas\Session\Service\SessionConfigFactory::class,// 'Laminas\Session\Service\SessionConfigFactory',
        ],
        'aliases' => [
            Adapter\Adapter::class => Adapter\AdapterInterface::class
        ]
    ],
    'db' => [
        'adapters' => [
            'Application\Db\WriteAdapter' => [
                'driver' => 'Pdo_Mysql',
                'dsn'    => 'mysql:dbname=saychas_z;host=localhost;charset=utf8',
                'driver_options' => [
                    PDO::MYSQL_ATTR_INIT_COMMAND =>'SET NAMES \'UTF8MB4\'',
                ],
                'username' => 'saychas_z',
                'password' => 'saychas_z',
            ],
            'Application\Db\ReadOnlyAdapter' => [
                'driver' => 'Pdo_Mysql',
                'dsn'    => 'mysql:dbname=saychas_z;host=localhost;charset=utf8',
            ],
        ],
        'driver' => 'Pdo_Mysql',
        'dsn' => 'mysql:dbname=saychas_z;hostname=localhost',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND =>'SET NAMES \'UTF8MB4\'',
        ],
    ],

    'session_containers' => [
        //Laminas\Session\Container::class,
        Container::class,
    ],
    'session_storage' => [
        'type' => SessionArrayStorage::class,
    ],
    'session_config'  => [
        // Session data will be stored on server maximum for 30 days.
        'gc_maxlifetime' => 60*60*24*30,
        // Session cookie will expire in 1 hour.
        'cookie_lifetime' => 60*60*24*30,// 60*60*1,
        //'remember_me_seconds' => 1,// 60*60*24*30,
        // ???
        'cache_expire' => 60*60*24*30,// 60*24,
//        'options' => [
//            //'remember_me_seconds' => 1,
////            'name' => 'laminas-shlaminas',
//            'name' => \ControlPanel\Resource\StringResource::CONTROL_PANEL_SESSION,
////            'name' => 'PHPSESSID',
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

];
