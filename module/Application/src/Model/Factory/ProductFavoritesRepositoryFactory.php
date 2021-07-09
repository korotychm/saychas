<?php

// src/Model/Factory/ProductFavoritesRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\ProductFavorites;
use Application\Model\Repository\ProductFavoritesRepository;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Hydrator\ReflectionHydrator;
//use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ProductFavoritesRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof ProductFavoritesRepository) {
            throw new Exception("not instanceof ProductFavoritesRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        //$hydrator = new ClassMethodsHydrator;
        $hydrator = new \Laminas\Hydrator\ReflectionHydrator;
        $prototype = new ProductFavorites;
        $prototype::$repository = new ProductFavoritesRepository(
                $adapter,
                $hydrator,
                $prototype
        );

        return $prototype::$repository;
    }

}
