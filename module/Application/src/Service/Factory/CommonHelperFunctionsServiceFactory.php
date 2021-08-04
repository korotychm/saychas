<?php

// Application\src\Service\Factory\CommonHelperFunctionsServiceFactory.php

namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Service\CommonHelperFunctionsService;

class CommonHelperFunctionsServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof CommonHelperFunctionsService) {
            throw new Exception("not instanceof CommonHelperFunctionsService");
        }

        $config = $container->get('Config');

        return new CommonHelperFunctionsService($config);
    }

}
