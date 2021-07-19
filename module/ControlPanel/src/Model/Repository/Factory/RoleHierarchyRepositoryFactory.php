<?php

// ControlPanel\src\Model\Repository\Factory\RoleHierarchyRepositoryFactory.php

namespace ControlPanel\Model\Repository\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ControlPanel\Model\Entity\Role;
use ControlPanel\Model\Entity\RoleHierarchy;
use ControlPanel\Model\Repository\RoleRepository;
use ControlPanel\Model\Repository\RoleHierarchyRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ClassMethodsHydrator;

/**
 * Description of EntityManagerFactory
 *
 * @author alex
 */
class RoleHierarchyRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof RoleHierarchyRepository) {
            throw new Exception("not instanceof RoleHierarchyRepository");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        $hydrator = new ClassMethodsHydrator;
        
        $prototype = new RoleHierarchy;
        
//        $prototype::$roleRepository = $container->get(Role::class);

        return new RoleHierarchyRepository(
                $adapter,
                $hydrator,
                $prototype
        );
    }

}
