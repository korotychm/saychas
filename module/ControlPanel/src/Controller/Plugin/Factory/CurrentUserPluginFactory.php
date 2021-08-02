<?php

// ControlPanel/src/Controller/Plugin/Factory/CurrentUserPluginFactory.php

namespace ControlPanel\Controller\Plugin\Factory;

use Interop\Container\ContainerInterface;
use ControlPanel\Controller\Plugin\CurrentUserPlugin;

class CurrentUserPluginFactory
{

    public function __invoke(ContainerInterface $container)
    {
        $userManager = $container->get(\ControlPanel\Service\UserManager::class);
        $authService = $container->get('my_auth_service');

        return new CurrentUserPlugin($userManager, $authService);
    }

}
