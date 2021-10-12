<?php

// src/Controller/Factory/ReviewControllerFactory.php

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Laminas\Authentication\AuthenticationService;
use Application\Model\RepositoryInterface\ProductFavoritesRepositoryInterface;
use Application\Model\RepositoryInterface\ProductHistoryRepositoryInterface;
use Application\Controller\ReviewController;
use Application\Service\CommonHelperFunctionsService;
use Application\Service\ExternalCommunicationService;

/**
 * This is the factory for ReviewController. Its purpose is to instantiate the
 * controller.
 */
class ReviewControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        //$productRating = $container->get(ProductRatingRepositoryInterface::class);
        $product = $container->get(ProductRepositoryInterface::class);
        //$handBookProduct = $container->get(HandbookRelatedProductRepositoryInterface::class);
        $entityManager = $container->get('laminas.entity.manager');
        $config = $container->get('Config');
        $authService = $container->get(AuthenticationService::class);
        $commonHelperFuncions = $container->get(CommonHelperFunctionsService::class);
        $externalCommunicationService = $container->get(ExternalCommunicationService::class);
        $container->get(ProductFavoritesRepositoryInterface::class);
        $container->get(ProductHistoryRepositoryInterface::class);
        
        
        return new ReviewController(
          //      $productRating, 
                $product, 
               // $handBookProduct, 
                $entityManager, 
                $config, 
                $authService,
                $commonHelperFuncions,
                $externalCommunicationService
                );
    }

}
