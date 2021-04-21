<?php
// src/Model/Factory/ProviderRelatedStoreRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Store;
use Application\Model\Repository\ProviderRelatedStoreRepository;
use Application\Model\Repository\ProviderRepository;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ProviderRelatedStoreRepositoryFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof ProviderRelatedStoreRepository){
            throw new Exception("not instanceof ProviderRelatedStoreRepository");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        
        $providerRepository = $container->get(ProviderRepository::class);
        $prototype = new Store;//new ProviderRelatedStore;
        $prototype::$providerRepository = $providerRepository;
        
        return new ProviderRelatedStoreRepository(
            $adapter,
            new ClassMethodsHydrator(),
            $prototype
        );
    }
}