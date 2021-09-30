<?php

// src/Controller/Factory/AjaxControllerFactory.php

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Laminas\Authentication\AuthenticationService;
use Application\Model\RepositoryInterface\ProductFavoritesRepositoryInterface;
use Application\Model\RepositoryInterface\ProductHistoryRepositoryInterface;
use Application\Controller\ProductCardsController;
use Application\Service\CommonHelperFunctionsService;

/**
 * This is the factory for ProductCardsController. Its purpose is to instantiate the
 * controller.
 */
class ProductCardsControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        $category = $container->get(CategoryRepositoryInterface::class);
        $characteristic = $container->get(CharacteristicRepositoryInterface::class);
        $handBookProduct = $container->get(HandbookRelatedProductRepositoryInterface::class);
        $entityManager = $container->get('laminas.entity.manager');
        $config = $container->get('Config');
        $authService = $container->get(AuthenticationService::class);
        $commonHelperFuncions = $container->get(CommonHelperFunctionsService::class);
        $container->get(ProductFavoritesRepositoryInterface::class);
        $container->get(ProductHistoryRepositoryInterface::class);
        
        
        return new ProductCardsController(
                $category, 
                $characteristic, 
                $handBookProduct, 
                $entityManager, 
                $config, 
                $authService,
                $commonHelperFuncions
                );
    }

}
