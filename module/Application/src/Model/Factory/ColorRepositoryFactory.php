<?php

// src/Model/Factory/ColorRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Color;
use Application\Model\Repository\ColorRepository;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ColorRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof ColorRepository) {
            throw new Exception("not instanceof ColorRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        return new ColorRepository(
                $adapter,
                new ClassMethodsHydrator,
                new Color
        );
    }

}
