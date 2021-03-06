<?php

// src/Adapter/Auth/Factory/UserAuthAdapterFactory.php

namespace Application\Adapter\Auth\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Adapter\Auth\UserAuthAdapter;
use Application\Model\Repository\UserRepository;
use Application\Model\Repository\UserDataRepository;
//use Laminas\Session\Container as SessionContainer;

class UserAuthAdapterFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof UserAuthAdapter) {
            throw new Exception("not instanceof UserAuthAdapter");
        }

        $adapter = $container->get(AdapterInterface::class);
        $userRepository = $container->get(UserRepository::class);
        $authService = $container->get(AuthenticationService::class);
//        $userDataRepository = $container->get(UserDataRepository::class);
        //$sessionContainer = $container->get(SessionContainer::class);

        return new UserAuthAdapter(
                $userRepository,
//                $sessionContainer,
                $adapter,
                $authService 
        );
    }

}
