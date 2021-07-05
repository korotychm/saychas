<?php

// src/Model/Factory/MarkerRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Marker;
use Application\Model\Repository\MarkerRepository;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class MarkerRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof MarkerRepository) {
            throw new Exception("not instanceof MarkerRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        return new MarkerRepository(
                $adapter,
                new ClassMethodsHydrator,
                new Marker
        );
    }

}
