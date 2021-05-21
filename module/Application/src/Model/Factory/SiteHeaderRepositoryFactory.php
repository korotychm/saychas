<?php

// src/Model/Factory/SiteHeaderRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\SiteHeader;
use Application\Model\Repository\SiteHeaderRepository;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SiteHeaderRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof SiteHeaderRepository) {
            throw new Exception("not instanceof SiteHeaderRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        return new SiteHeaderRepository(
                $adapter,
                new ClassMethodsHydrator,
                new SiteHeader
        );
    }

}
