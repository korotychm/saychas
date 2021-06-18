<?php

// src/Controller/Factory/AjaxControllerFactory.php

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
//use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Model\TestRepositoryInterface;
use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
use Application\Model\RepositoryInterface\ProviderRepositoryInterface;
use Application\Model\RepositoryInterface\StoreRepositoryInterface;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;
use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;
use Application\Model\RepositoryInterface\BrandRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Application\Model\RepositoryInterface\ProductCharacteristicRepositoryInterface;
use Application\Model\Repository\UserRepository;
use Laminas\Authentication\AuthenticationService;

use Application\Service\HtmlProviderService;
use Application\Controller\AjaxController;

/**
 * This is the factory for AjaxController. Its purpose is to instantiate the
 * controller.
 */
class AjaxControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        // Instantiate the controller and inject dependencies
        $test = $container->get(/*                 * \Application\Model\ */TestRepositoryInterface::class);
        $category = $container->get(CategoryRepositoryInterface::class);
        $provider = $container->get(ProviderRepositoryInterface::class);
        $store = $container->get(StoreRepositoryInterface::class);
        $product = $container->get(ProductRepositoryInterface::class);
        $filteredProduct = $container->get(FilteredProductRepositoryInterface::class);
        $brand = $container->get(BrandRepositoryInterface::class);
        $characteristic = $container->get(CharacteristicRepositoryInterface::class);
        $price = $container->get(PriceRepositoryInterface::class);
        $stockBalance = $container->get(StockBalanceRepositoryInterface::class);
        $handBookProduct = $container->get(HandbookRelatedProductRepositoryInterface::class);
        $productCharacteristicRepository = $container->get(ProductCharacteristicRepositoryInterface::class);
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $config = $container->get('Config');
        $htmlProvider = $container->get(HtmlProviderService::class);
        $userRepository = $container->get(UserRepository::class);
        $authService = $container->get(AuthenticationService::class);
        return new AjaxController($test, $category, $provider, $store, $product, $filteredProduct, $brand, $characteristic, $price, 
                $stockBalance, $handBookProduct, $entityManager, $config, $htmlProvider, $userRepository, $authService,
                $productCharacteristicRepository);
    }

}
