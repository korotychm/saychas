<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Category;
use Application\Model\LaminasDbSqlRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class LaminasDbSqlRepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        
        if($requestedName instanceof CategoryRepository){
            throw new Exception("not instanceof CategoryRepository");
        }

        $adapter = $container->get(AdapterInterface::class);
        return new LaminasDbSqlRepository(
            $adapter,
            new ReflectionHydrator(),
            new Category('', '')
        );
    }
}