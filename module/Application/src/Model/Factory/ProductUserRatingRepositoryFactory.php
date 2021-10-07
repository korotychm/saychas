<?php

// src/Model/Factory/ProductUserRatingRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\ProductUserRating;
use Application\Model\Repository\ProductUserRatingRepository;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ProductUserRatingRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof ProductUserRatingRepository) {
            throw new Exception("not instanceof ProductUserRatingRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        return new ProductUserRatingRepository(
                $adapter,
                new ClassMethodsHydrator,
                new ProductUserRating
        );
    }

}
