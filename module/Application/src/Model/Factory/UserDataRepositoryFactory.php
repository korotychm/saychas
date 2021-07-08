<?php

// src/Model/Factory/UserDataRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\UserData;
use Application\Model\Repository\UserDataRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

class UserDataRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof UserDataRepository) {
            throw new Exception("not instanceof UserDataRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

//        $hydrator = new \Laminas\Hydrator\Aggregate\AggregateHydrator();
//        $hydrator->add(new \Laminas\Hydrator\ClassMethodsHydrator);
//        $hydrator->add(new \Laminas\Hydrator\ReflectionHydrator);
//        $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();
        $hydrator = new \Laminas\Hydrator\ReflectionHydrator;
//        $hydrator->addStrategy(
//            'timestamp',
//            new DateTimeFormatterStrategy(
//                'Y-m-d'
//            )
//        );

        //$userDataRepository = $container->get(UserDataRepository::class);
        
        $prototype = new UserData;
        $prototype::$userDataRepository = new UserDataRepository(
                $adapter,
                $hydrator,
                $prototype
        );

        return $prototype::$userDataRepository;
//        return new UserDataRepository(
//                $adapter,
//                $hydrator,
//                $prototype
//        );
    }

}
