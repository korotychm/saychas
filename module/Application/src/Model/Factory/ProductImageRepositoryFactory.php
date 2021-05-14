<?php

// src/Model/Factory/ProductImageRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\ProductImage;
use Application\Model\Repository\ProductImageRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\Aggregate\AggregateHydrator;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ProductImageRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof ProductImageRepository) {
            throw new Exception("not instanceof ProductImageRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        $hydrator = new AggregateHydrator();
        $hydrator->add(new ReflectionHydrator());
        $hydrator->add(new ClassMethodsHydrator);
        return new ProductImageRepository(
                $adapter,
                new ReflectionHydrator(),
                new ProductImage
        );
    }

}
