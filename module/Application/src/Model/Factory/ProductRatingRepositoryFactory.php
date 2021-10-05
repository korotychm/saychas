<?php

// src/Model/Factory/ProductRatingRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\ProductRating;
use Application\Model\Repository\ProductRatingRepository;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ProductRatingRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof ProductRatingRepository) {
            throw new Exception("not instanceof ProductRatingRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        return new ProductRatingRepository(
                $adapter,
                new ClassMethodsHydrator,
                new ProductRating
        );
    }

}
