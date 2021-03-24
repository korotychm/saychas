<?php
// src/Model/Factory/ProviderRepositoryFactory.php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Provider;
use Application\Model\Repository\ProviderRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ProviderRepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        if($requestedName instanceof ProviderRepository){
            throw new Exception("not instanceof ProviderRepository");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        
        return new ProviderRepository(
            $adapter,
            new ReflectionHydrator(),
            new Provider('', 0, 0, null, null)
        );
    }
}