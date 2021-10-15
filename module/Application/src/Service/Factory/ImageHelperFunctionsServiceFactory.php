<?php

// Application\src\Service\Factory\ImageHelperFunctionsServiceFactory.php

namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Service\ImageHelperFunctionsService;

//use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;

class ImageHelperFunctionsServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof ImageHelperFunctionsService) {
            throw new Exception("not instanceof ImageHelperFunctionsService");
        }

        $config = $container->get('Config');
        $entityManager = $container->get('laminas.entity.manager');
    
        return new ImageHelperFunctionsService($config, $entityManager);
    }

}
