<?php

// ControlPanel\src\Service\Factory\RbacAssertionManagerFactory.php

namespace ControlPanel\Service\Factory;

use Interop\Container\ContainerInterface;
use ControlPanel\Service\RbacAssertionManager;
//use Laminas\Authentication\AuthenticationService;

/**
 * This is the factory class for RbacAssertionManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class RbacAssertionManagerFactory
{

    /**
     * This method creates the NavManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        //$authService = $container->get(AuthenticationService::class);
        $authService = $container->get('my_auth_service');
        $userManager = $container->get(\ControlPanel\Service\UserManager::class);
        $productManager = $container->get(\ControlPanel\Service\ProductManager::class);

        return new RbacAssertionManager($container, $authService, $userManager, $productManager);
    }

}
