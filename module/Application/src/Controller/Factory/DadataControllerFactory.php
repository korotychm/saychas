<?php

// src/Controller/Factory/DadataControllerFactory.php

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
//use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
//use Application\Model\RepositoryInterface\ProductRepositoryInterface;
//use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Laminas\Authentication\AuthenticationService;
use Application\Controller\DadataController;
use Application\Service\DadataService;
//use Application\Model\RepositoryInterface\ProductFavoritesRepositoryInterface;
//use Application\Model\RepositoryInterface\ProductHistoryRepositoryInterface;
//use Application\Service\CommonHelperFunctionsService;
//use Application\Service\ImageHelperFunctionsService;
//use Application\Service\ExternalCommunicationService;

/**
 * This is the factory for DadataController. Its purpose is to instantiate the
 * controller.
 */
class DadataControllerFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
     
        $config = $container->get('Config');
        $authService = $container->get(AuthenticationService::class);
        $dadataService = $container->get(DadataService::class);
        //$externalCommunicationService = $container->get(ExternalCommunicationService::class);
        return new DadataController(
                $config, 
                $authService,
                $dadataService
                );
    }

}
