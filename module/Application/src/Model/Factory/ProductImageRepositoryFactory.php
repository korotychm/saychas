<?php
// src/Model/Factory/ProductImageRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\ProductImage;
use Application\Model\Repository\ProductImageRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ProductImageRepositoryFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof ProductImageRepository){
            throw new Exception("not instanceof ProductImageRepository");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        
        return new ProductImageRepository(
            $adapter,
            new ReflectionHydrator(),
            new ProductImage(0,'', '', '', 0)
        );
    }
}