<?php

// /module/src/View/Helper/Factory/DocumentPathFactory.php

namespace Application\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
//use Laminas\Authentication\AuthenticationService;
use Application\View\Helper\DocumentPath;

/**
 * This is the factory for ImagePath view helper. Its purpose is to instantiate the helper
 * and inject dependencies into its constructor.
 */
class DocumentPathFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        //$authService = $container->get(AuthenticationService::class);
        $authService = $container->get('my_auth_service');

        $identity = $authService->getIdentity();

        return new DocumentPath($identity['provider_id']);
    }

}
