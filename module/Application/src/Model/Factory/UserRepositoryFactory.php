<?php
// src/Model/Factory/UserRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\User;
use Application\Model\Entity\Entity;
use Application\Model\Repository\UserRepository;
use Laminas\Db\Adapter\AdapterInterface;
// use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class UserRepositoryFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof UserRepository){
            throw new Exception("not instanceof UserRepository");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        
        return new UserRepository(
            $adapter,
            new \Laminas\Hydrator\ClassMethodsHydrator(),//new ReflectionHydrator(),
            new User // new User('', '', '', '')
        );
    }
}