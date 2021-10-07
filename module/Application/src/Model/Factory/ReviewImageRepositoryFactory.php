<?php

// src/Model/Factory/ReviewImageRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\ReviewImage;
use Application\Model\Repository\ReviewImageRepository;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ReviewImageRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof ReviewImageRepository) {
            throw new Exception("not instanceof ReviewImageRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        $hydrator = new ClassMethodsHydrator;
        $prototype = new ReviewImage;
        $prototype::$repository = new ReviewImageRepository(
                $adapter,
                $hydrator,
                $prototype
        );

        return $prototype::$repository;
    }

}
