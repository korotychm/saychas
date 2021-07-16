<?php

// ControlPanel\src\Service\Factory\EntityManagerFactory.php

namespace ControlPanel\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ControlPanel\Service\EntityManager;

/**
 * Description of EntityManagerFactory
 *
 * @author alex
 */
class EntityManagerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof EntityManager) {
            throw new Exception("not instanceof EntityManager");
        }
        
        return new EntityManager($container);
    }

}
