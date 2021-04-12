<?php
// src/Model/Factory/PredefCharValueRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\PredefCharValue;
use Application\Model\Repository\PredefCharValueRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class PredefCharValueRepositoryFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof PredefCharValueRepository){
            throw new Exception("not instanceof CharacteristicRepository");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        
        return new PredefCharValueRepository(
            $adapter,
            new ReflectionHydrator(),
            new PredefCharValue('', '', '')
        );
    }
}