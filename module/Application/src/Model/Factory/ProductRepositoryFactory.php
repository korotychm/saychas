<?php
// src/Model/Factory/ProductRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Product;
//use Application\Model\Entity\ProductImage;
use Application\Model\Repository\ProductRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Model\RepositoryInterface\CharacteristicValueRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicValue2RepositoryInterface;
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
        $characteristicValue = $container->get(CharacteristicValueRepositoryInterface::class);
        $caracteristicValue2 = $container->get(CharacteristicValue2RepositoryInterface::class);
        $characteristics = $container->get(CharacteristicRepositoryInterface::class);
        $productImages = $container->get(ProductImageRepositoryInterface::class);
        
        $config = $container->get('Config');
        $catalogToSaveImages = $config['parameters']['catalog_to_save_images'];
        
        return new ProductRepository(
            $adapter,
            new ReflectionHydrator(),
            new Product(0, 0, 0, '', '', '', 0, 0, '', ''),
            $characteristicValue,
            $characteristics,
            $productImages,
            $caracteristicValue2,
            $catalogToSaveImages,
        );
    }
}