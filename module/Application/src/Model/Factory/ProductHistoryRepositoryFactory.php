<?php

// src/Model/Factory/ProductHistoryRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\ProductHistory;
use Application\Model\Repository\ProductHistoryRepository;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Hydrator\ReflectionHydrator;
//use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ProductHistoryRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof ProductHistoryRepository) {
            throw new Exception("not instanceof ProductHistoryRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

//        $hydrator = new ClassMethodsHydrator;
        $hydrator = new \Laminas\Hydrator\ReflectionHydrator;
        $prototype = new ProductHistory;
        $prototype::$repository = new ProductHistoryRepository(
                $adapter,
                $hydrator,
                $prototype
        );

        return $prototype::$repository;
    }

}
