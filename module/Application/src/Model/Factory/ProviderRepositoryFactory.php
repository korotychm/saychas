<?php

// src/Model/Factory/ProviderRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Provider;
use Application\Model\Repository\ProviderRepository;
use Application\Model\Repository\StoreRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
//use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ProviderRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof ProviderRepository) {
            throw new Exception("not instanceof ProviderRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        $hydrator = new ReflectionHydrator(); // new ClassMethodsHydrator();//

        $storeRepository = $container->get(StoreRepository::class);
        
        $entityManager = $container->get('laminas.entity.manager');

        $prototype = new Provider;

        $prototype::$storeRepository = $storeRepository;

        return new ProviderRepository(
                $adapter,
                $hydrator,
                $prototype,
                $entityManager
        );
    }

}
