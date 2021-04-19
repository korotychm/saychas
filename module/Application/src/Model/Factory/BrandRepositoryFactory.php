<?php
// src/Model/Factory/BrandRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Brand;
use Application\Model\Repository\BrandRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class BrandRepositoryFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof BrandRepository){
            throw new Exception("not instanceof BrandRepository");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        
        return new BrandRepository(
            $adapter,
            new ReflectionHydrator(),
            new Brand('', '', '', '')
        );
    }
}