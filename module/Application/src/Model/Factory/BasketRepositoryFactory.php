<?php

// src/Model/Factory/BasketRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Basket;
use Application\Model\Repository\BasketRepository;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class BasketRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof BasketRepository) {
            throw new Exception("not instanceof BasketRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        $hydrator = new ClassMethodsHydrator;
        $prototype = new Basket;
        $prototype::$repository = new BasketRepository(
                $adapter,
                $hydrator,
                $prototype
        );

        return $prototype::$repository;
        
//        return new BasketRepository(
//                $adapter,
//                $hydrator, // new ClassMethodsHydrator,
//                $prototype // new Basket
//        );
    }

}
