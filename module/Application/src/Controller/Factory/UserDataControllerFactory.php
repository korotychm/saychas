<?php

// src/Controller/Factory/UserDataControllerFactory.php

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Model\Repository\UserRepository;
use Application\Controller\UserDataController;
use Laminas\Authentication\AuthenticationService;
//use Laminas\Session\Container as SessionContainer;
use Laminas\Db\Adapter\AdapterInterface;
//use Application\Adapter\Auth\UserAuthAdapter;
use Application\Service\ExternalCommunicationService;
use Application\Service\CommonHelperFunctionsService;
/**
 * This is the factory for UserDataController. Its purpose is to instantiate the
 * controller.
 */
class UserDataControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        // Instantiate the controller and inject dependencies
        $userRepository = $container->get(UserRepository::class);
        //$config = $container->get('Config');
        $adapter = $container->get(AdapterInterface::class);
        $authService = $container->get(AuthenticationService::class);
//        $sessionContainer = $container->get(SessionContainer::class);
        //$userAdapter = $container->get(UserAuthAdapter::class);
        $commonHelperFuncions = $container->get(CommonHelperFunctionsService::class);
        $externalCommunicationService = $container->get(ExternalCommunicationService::class);
        $entityManager = $container->get('laminas.entity.manager');
        return new UserDataController($userRepository/*, $config*/, $authService, $adapter/*, $userAdapter*/, $externalCommunicationService/*, $sessionContainer*/, $commonHelperFuncions, $entityManager);
    }

}
