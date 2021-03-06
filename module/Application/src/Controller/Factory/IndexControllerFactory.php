<?php

// src/Controller/Factory/IndexControllerFactory.php

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
//use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Session\Container;// as SessionContainer;
use Laminas\Session\SessionManager;
use Application\Model\TestRepositoryInterface;
use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
use Application\Model\RepositoryInterface\ProviderRepositoryInterface;
use Application\Model\RepositoryInterface\StoreRepositoryInterface;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;
use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;
use Application\Model\RepositoryInterface\BrandRepositoryInterface;
use Application\Model\RepositoryInterface\BasketRepositoryInterface;
use Application\Model\RepositoryInterface\ColorRepositoryInterface;
use Application\Model\RepositoryInterface\SettingRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Application\Model\RepositoryInterface\ProductCharacteristicRepositoryInterface;

use Application\Model\RepositoryInterface\ProductFavoritesRepositoryInterface;
use Application\Model\RepositoryInterface\ProductHistoryRepositoryInterface;

use Application\Model\Repository\UserRepository;
use Application\Service\HtmlProviderService;
use Application\Service\CommonHelperFunctionsService;
use Application\Service\HtmlFormProviderService;
use Application\Controller\IndexController;

/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller.
 */
class IndexControllerFactory implements FactoryInterface
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
        $setting = $container->get(SettingRepositoryInterface::class);
        $characteristic = $container->get(CharacteristicRepositoryInterface::class);
        $price = $container->get(PriceRepositoryInterface::class);
        $stockBalance = $container->get(StockBalanceRepositoryInterface::class);
        $handBookProduct = $container->get(HandbookRelatedProductRepositoryInterface::class);
        //$entityManager = $container->get('doctrine.entitymanager.orm_default');
        $entityManager = $container->get('laminas.entity.manager');
        $config = $container->get('Config');
        $htmlProvider = $container->get(HtmlProviderService::class);
        $htmlFormProvider = $container->get(HtmlFormProviderService::class);
        $commonHelperFuncions = $container->get(CommonHelperFunctionsService::class);
        $userRepository = $container->get(UserRepository::class);
        $authService = $container->get(AuthenticationService::class);
        $productCharacteristic = $container->get(ProductCharacteristicRepositoryInterface::class);
        $colorRepository = $container->get(ColorRepositoryInterface::class);
        $basketRepository = $container->get(BasketRepositoryInterface::class);
        
        $container->get(ProductFavoritesRepositoryInterface::class);
        $container->get(ProductHistoryRepositoryInterface::class);
        
        //$sessionContainer = $container->get(SessionContainer::class);
        $sessionManager = $container->get(SessionManager::class);
        
        return new IndexController($test, $category, $provider, $store, $product, $filteredProduct, $brand, $colorRepository, $setting, $characteristic,
                $price, $stockBalance, $handBookProduct, $commonHelperFuncions, $entityManager, $config, $htmlProvider, $htmlFormProvider,  $userRepository, $authService, $productCharacteristic,
                $basketRepository/*, $sessionContainer*/, $sessionManager);
    }

}
