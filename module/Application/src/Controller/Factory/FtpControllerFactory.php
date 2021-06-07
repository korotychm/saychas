<?php

// src/Controller/Factory/FtpControllerFactory.php

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for FtpController. Its purpose is to instantiate the
 * controller.
 */
class FtpControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        // Instantiate the controller and inject dependencies
        $config = $container->get('Config');
        return new $requestName($config);
    }

}
