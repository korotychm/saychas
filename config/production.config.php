<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */
//echo 'development.config.php';
//exit;
return [
//    'view_manager' => [
//        'display_exceptions' => true,
//    ],
//    'db' => [
//        'username' => 'saychas_z',
//        'password' => 'saychas_z',
//    ],
    // Additional modules to include when in development mode
//    'modules' => [
//    ],
    // Configuration overrides during development mode
    'module_listener_options' => [
        'config_glob_paths' => [realpath(__DIR__) . '/autoload/{,*.}{global,local}-production.php'],
        //realpath(__DIR__) . sprintf('/autoload/{,*.}{global,%s,local}.php', getenv('APP_ENV') ?: 'production'),
        'config_cache_enabled' => false,
        'module_map_cache_enabled' => true,
    ],
];
