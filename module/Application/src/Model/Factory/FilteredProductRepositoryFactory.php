<?php
// src/Model/Factory/FilteredProductRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\FilteredProduct;
use Application\Model\Repository\FilteredProductRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FilteredProductRepositoryFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof FilteredProductRepository){
            throw new Exception("not instanceof ProductRepository");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        
        return new FilteredProductRepository(
            $adapter,
            new ReflectionHydrator(),
            new FilteredProduct(0, 0, 0, 0, '', 0, '', '')
        );
    }
}