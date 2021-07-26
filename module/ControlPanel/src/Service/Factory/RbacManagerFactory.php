<?php

// ControlPanel\src\Service\Factory\RbacManagerFactory.php

namespace ControlPanel\Service\Factory;

use Interop\Container\ContainerInterface;
use ControlPanel\Service\RbacManager;
//use Laminas\Authentication\AuthenticationService;

class RbacManagerFactory
{

    /**
     * This method creates the RbacManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        //$authService = $container->get(AuthenticationService::class);
        $authService = $container->get('my_auth_service');
        $cache = $container->get('FilesystemCache');

        $assertionManagers = [];
        $config = $container->get('Config');
        if (isset($config['rbac_manager']['assertions'])) {
            foreach ($config['rbac_manager']['assertions'] as $serviceName) {
                $assertionManagers[$serviceName] = $container->get($serviceName);
            }
        }
        
        $entityManager = $container->get('laminas.entity.manager');

        return new RbacManager($entityManager, $authService, $cache, $assertionManagers);
    }

}
