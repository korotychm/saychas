<?php


return [
    'table_storage' => [
        'table_name' => 'doctrine_migrations',
        'version_column_name' => 'version',
        'version_column_length' => 1024,
        'executed_at_column_name' => 'executed_at',
        'execution_time_column_name' => 'execution_time',
    ],

    'migrations_paths' => [
        'SaychasProjectZ\Migrations' => '/data/doctrine/migrations',
        //'SaychasProjectZ\Component\Migrations' => './Component/SaychasProjectZ/Migrations',
    ],
    //'directory' => 'data/doctrine/migrations', // '/',

    'all_or_nothing' => true,
    'check_database_platform' => true,
    'organize_migrations' => 'none',
];
 
 
/**
return [
	'table_storage' => [
    //'name' => 'My Project Migrations',
    //'migrations_namespace' => 'MyProject\Migrations',
    'table_name' => 'doctrine_migration_versions',
//    'column_name' => 'version',
//    'column_length' => 14,
    'executed_at_column_name' => 'executed_at',
    //'migrations_directory' => '/data/doctrine/migrations', //  '/data/doctrine/migrations-docs-example/lib/MyProject/Migrations',
    'migrations_paths' => ['SaychasProjectZ\Migrations' => '/data/doctrine/migrations',],
    'all_or_nothing' => true,
    ],
];
*/
