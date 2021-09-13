<?php

// ControlPanel/src/Controller/Factory/ProductControllerFactory.php

namespace ControlPanel\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Container;
use ControlPanel\Resource\StringResource;
use Application\Model\Repository\CategoryRepository;
//use Application\Service\EntityManager;
//use ControlPanel\Service\UserManager;

/**
 * This is the factory for Index Controllers. Its purpose is to instantiate the
 * index controllers.
 */
class ProductControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        // Instantiate the controller and inject dependencies
        $sessionContainer = new Container(StringResource::CONTROL_PANEL_SESSION);
        $entityManager = $container->get('laminas.entity.manager');
        $categoryRepository = $container->get(CategoryRepository::class);
        return new $requestName($container, $sessionContainer, $entityManager, $categoryRepository);
    }

}
