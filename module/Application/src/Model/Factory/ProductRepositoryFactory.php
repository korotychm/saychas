<?php
// src/Model/Factory/ProductRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Product;
use Application\Model\Entity\ProductImage;
use Application\Model\Repository\ProductRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Model\RepositoryInterface\PredefCharValueRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\ProductImageRepositoryInterface;

class ProductRepositoryFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof ProductRepository){
            throw new Exception("not instanceof ProductRepository");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        $predefChar = $container->get(PredefCharValueRepositoryInterface::class);
        $characteristics = $container->get(CharacteristicRepositoryInterface::class);
        $productImages = $container->get(ProductImageRepositoryInterface::class);
        
        return new ProductRepository(
            $adapter,
            new ReflectionHydrator(),
            new Product(0, 0, 0, '', '', '', 0, 0, '', ''),
            $predefChar,
            $characteristics,
            $productImages
        );
    }
}