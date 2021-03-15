<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Store;
use Application\Model\Repository\StoreRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class StoreRepositoryFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof StoreRepository){
            throw new Exception("not instanceof StoreRepository");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        
        return new StoreRepository(
            $adapter,
            new ReflectionHydrator(),
            new Store(0, '', '', '', '', '', '', '')
        );
    }
}