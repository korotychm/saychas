<?php
// src/Model/Factory/CharacteristicValueRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\CharacteristicValue;
use Application\Model\Repository\CharacteristicValueRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class CharacteristicValueRepositoryFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof CharacteristicValueRepository){
            throw new Exception("not instanceof CharacteristicRepository");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        
        return new CharacteristicValueRepository(
            $adapter,
            new ReflectionHydrator(),
            new CharacteristicValue//('', '', '')
        );
    }
}