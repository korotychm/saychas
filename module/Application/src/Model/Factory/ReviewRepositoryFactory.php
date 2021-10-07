<?php

// src/Model/Factory/ReviewRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Review;
use Application\Model\Repository\ReviewRepository;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ReviewRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof ReviewRepository) {
            throw new Exception("not instanceof ReviewRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        $hydrator = new ClassMethodsHydrator;
        $prototype = new Review;
        $prototype::$repository = new ReviewRepository(
                $adapter,
                $hydrator,
                $prototype
        );

        return $prototype::$repository;
        
//        return new ReviewRepository(
//                $adapter,
//                $hydrator, // new ClassMethodsHydrator,
//                $prototype // new Review
//        );
    }

}
