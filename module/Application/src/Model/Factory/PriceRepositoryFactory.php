<?php

// src/Model/Factory/PriceRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Price;
use Application\Model\Repository\PriceRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\Aggregate\AggregateHydrator;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class PriceRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof PriceRepository) {
            throw new Exception("not instanceof PriceRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        $hydrator = new AggregateHydrator();
        $hydrator->add(new ReflectionHydrator());
        $hydrator->add(new ClassMethodsHydrator);

        return new PriceRepository(
                $adapter,
                $hydrator,
                new Price
        );
    }

}
