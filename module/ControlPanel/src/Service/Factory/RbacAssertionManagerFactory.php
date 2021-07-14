<?php

// ControlPanel\src\Service\Factory\RbacAssertionManagerFactory.php

namespace ControlPanel\Service\Factory;

use Interop\Container\ContainerInterface;
use ControlPanel\Service\RbacAssertionManager;
use Laminas\Authentication\AuthenticationService;

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
        $authService = $container->get(AuthenticationService::class);

        return new RbacAssertionManager($authService);
    }

}
