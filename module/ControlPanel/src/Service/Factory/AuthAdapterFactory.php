<?php
namespace ControlPanel\Service\Factory;

use Interop\Container\ContainerInterface;
use ControlPanel\Service\AuthAdapter;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ControlPanel\Service\UserManager;

/**
 * This is the factory class for AuthAdapter service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class AuthAdapterFactory implements FactoryInterface
{
    /**
     * This method creates the AuthAdapter service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        // Get Doctrine entity manager from Service Manager.
        $entityManager = $container->get('laminas.entity.manager');
        $userManager = $container->get(UserManager::class);
                        
        // Create the AuthAdapter and inject dependency to its constructor.
        return new AuthAdapter($entityManager, $userManager);
    }
}
