<?php

// ControlPanel/src/Controller/Factory/StandardControllerFactory.php

namespace ControlPanel\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Container;
use ControlPanel\Resource\StringResource;

/**
 * This is the factory for Standard Controllers. Its purpose is to instantiate the
 * standard controllers.
 */
class StandardControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        // Instantiate the controller and inject dependencies
        $sessionContainer = new Container(StringResource::CONTROL_PANEL_SESSION);
        return new $requestName($container, $sessionContainer);
    }

}
