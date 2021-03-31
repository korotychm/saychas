<?php
// src/Model/Factory/ProductRepositoryFactory.php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Product;
use Application\Model\Repository\ProductRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ProductRepositoryFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof ProductRepository){
            throw new Exception("not instanceof ProductRepository");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        
        return new ProductRepository(
            $adapter,
            new ReflectionHydrator(),
            new Product(0, 0, 0, '', '', '', 0, 0, '', '')
        );
    }
}