<?php

// src/Model/Factory/PriceRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Characteristic;
use Application\Model\Repository\CharacteristicRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class CharacteristicRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof CharacteristicRepository) {
            throw new Exception("not instanceof CharacteristicRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        return new CharacteristicRepository(
                $adapter,
                new ReflectionHydrator(),
                new Characteristic//('', '', '', 0, 0, 0, 0, '', '')
        );
    }

}
