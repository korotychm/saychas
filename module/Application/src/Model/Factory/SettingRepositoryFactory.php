<?php

// src/Model/Factory/SettingRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Setting;
use Application\Model\Repository\SettingRepository;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SettingRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof SettingRepository) {
            throw new Exception("not instanceof SettingRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        return new SettingRepository(
                $adapter,
                new ClassMethodsHydrator,
                new Setting
        );
    }

}
