<?php

// src/Controller/Factory/ReceivingControllerFactory.php

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Controller\ReceivingController;

/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller.
 */
class ReceivingControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        // Instantiate the controller and inject dependencies
        return new ReceivingController($container);
    }

}
