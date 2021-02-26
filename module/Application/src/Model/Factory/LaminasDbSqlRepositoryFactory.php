<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// In module/Application/src/Model/Factory/LaminasDbSqlRepositoryFactory.php
//namespace Application\Model\Factory;
//
//use Interop\Container\ContainerInterface;
//use Application\Model\LaminasDbSqlRepository;
////use \Laminas\Db\Adapter\AdapterInterface;
//use \Laminas\Db\Adapter\Adapter;
//use Laminas\ServiceManager\Factory\FactoryInterface;
//
//class LaminasDbSqlRepositoryFactory implements FactoryInterface
//{
//    /**
//     * @param ContainerInterface $container
//     * @param string $requestedName
//     * @param null|array $options
//     * @return LaminasDbSqlRepository
//     */
//    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
//    {
//        $adapter = new Adapter([
//            'driver'   => 'Pdo_Mysql',
//            'database' => 'saychas_z',
//            'username' => 'saychas_z',
//            'password' => 'saychas_z',
//        ]);
//        return new LaminasDbSqlRepository($adapter);
////        return new LaminasDbSqlRepository($container->get(AdapterInterface::class));
//    }
//}

// In /module/Blog/src/Factory/LaminasDbSqlRepositoryFactory.php
namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Test;
use Application\Model\LaminasDbSqlRepository;
//use Laminas\Db\Adapter\AdapterInterface;
use \Laminas\Db\Adapter\Adapter;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class LaminasDbSqlRepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $adapter = new Adapter([
            'driver'   => 'Pdo_Mysql',
            'database' => 'saychas_z',
            'username' => 'saychas_z',
            'password' => 'saychas_z',
        ]);
        return new LaminasDbSqlRepository(
//            $container->get(AdapterInterface::class),
            $adapter,
            new ReflectionHydrator(),
            new Test('', '')
        );
    }
}