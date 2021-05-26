<?php

// src/Model/Factory/ProductCharacteristicRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\ProductCharacteristic;
use Application\Model\Repository\ProductCharacteristicRepository;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ProductCharacteristicRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof ProductCharacteristicRepository) {
            throw new Exception("not instanceof ProductCharacteristicRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        return new ProductCharacteristicRepository(
                $adapter,
                new ClassMethodsHydrator,
                new ProductCharacteristic
        );
    }

}
