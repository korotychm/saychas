<?php

// src/Model/Factory/UserPaycardRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\UserPaycard;
use Application\Model\Repository\UserPaycardRepository;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class UserPaycardRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof UserPaycardRepository) {
            throw new Exception("not instanceof BasketRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        $hydrator = new ClassMethodsHydrator;
        $prototype = new UserPaycard;
        $prototype::$repository = new UserPaycardRepository(
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
