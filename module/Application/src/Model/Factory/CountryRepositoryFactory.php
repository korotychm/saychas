<?php

// src/Model/Factory/CountryRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Country;
use Application\Model\Repository\CountryRepository;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class CountryRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof CountryRepository) {
            throw new Exception("not instanceof CountryRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        return new CountryRepository(
                $adapter,
                new ClassMethodsHydrator,
                new Country
        );
    }

}
