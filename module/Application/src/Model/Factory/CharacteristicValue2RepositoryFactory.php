<?php
// src/Model/Factory/CharacteristicValue2RepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\CharacteristicValue2;
use Application\Model\Repository\CharacteristicValue2Repository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class CharacteristicValue2RepositoryFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof CharacteristicValue2Repository){
            throw new Exception("not instanceof Characteristic2Repository");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        
        return new CharacteristicValue2Repository(
            $adapter,
            new ReflectionHydrator(),
            new CharacteristicValue2('', '', '')
        );
    }
}