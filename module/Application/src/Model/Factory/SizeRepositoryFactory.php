<?php

// src/Model/Factory/SizeRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Size;
use Application\Model\Repository\SizeRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SizeRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof SizeRepository) {
            throw new Exception("not instanceof SizeRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        $hydrator = new ClassMethodsHydrator();

        return new SizeRepository(
                $adapter,
                $hydrator,
                new Size
        );
    }

}
