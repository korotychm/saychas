<?php

// ControlPanel\src\Model\Repository\Factory\RoleRepositoryFactory.php

namespace ControlPanel\Model\Repository\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ControlPanel\Model\Entity\Role;
use ControlPanel\Model\Repository\RoleRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ClassMethodsHydrator;

/**
 * Description of EntityManagerFactory
 *
 * @author alex
 */
class RoleRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof RoleRepository) {
            throw new Exception("not instanceof RoleRepository");
        }
        
        $adapter = $container->get(AdapterInterface::class);

        return new RoleRepository(
                $adapter,
                new ClassMethodsHydrator,
                new Role
        );
    }

}
