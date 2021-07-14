<?php

// src/Controller/Factory/MyTestControllerFactory.php

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Model\TestRepositoryInterface;
use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
use Application\Model\RepositoryInterface\ProviderRepositoryInterface;
use Application\Model\RepositoryInterface\StoreRepositoryInterface;
use Application\Model\RepositoryInterface\ProviderRelatedStoreRepositoryInterface;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;
use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;
use Application\Model\RepositoryInterface\BrandRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicValueRepositoryInterface;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Application\Model\RepositoryInterface\ProductFavoritesRepositoryInterface;
use Application\Model\RepositoryInterface\ProductHistoryRepositoryInterface;
use Application\Model\Repository\UserRepository;
use Application\Service\HtmlProviderService;
use Application\Service\HtmlFormProviderService;
use Application\Controller\MyTestController;
use Laminas\Authentication\AuthenticationService;
use Laminas\Db\Adapter\AdapterInterface;
use Application\Adapter\Auth\UserAuthAdapter;

/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller.
 */
class MyTestControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        // Instantiate the controller and inject dependencies
        $test = $container->get(/*                 * \Application\Model\ */TestRepositoryInterface::class);
        $category = $container->get(CategoryRepositoryInterface::class);
        $provider = $container->get(ProviderRepositoryInterface::class);
        $store = $container->get(StoreRepositoryInterface::class);
        $providerRelatedStore = $container->get(ProviderRelatedStoreRepositoryInterface::class);
        $product = $container->get(ProductRepositoryInterface::class);
        $filteredProduct = $container->get(FilteredProductRepositoryInterface::class);
        $brand = $container->get(BrandRepositoryInterface::class);
        $characteristic = $container->get(CharacteristicRepositoryInterface::class);
        $characteristicValue = $container->get(CharacteristicValueRepositoryInterface::class);
        $price = $container->get(PriceRepositoryInterface::class);
        $stockBalance = $container->get(StockBalanceRepositoryInterface::class);
        $handBookProduct = $container->get(HandbookRelatedProductRepositoryInterface::class);
        $userRepository = $container->get(UserRepository::class);
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $config = $container->get('Config');
        $htmlProvider = $container->get(HtmlProviderService::class);
        $htmlFormProvider = $container->get(HtmlFormProviderService::class);
        //$authService = $container->get('my_auth_service');
        $adapter = $container->get(AdapterInterface::class);
        $authService = $container->get(AuthenticationService::class);
        $userAdapter = $container->get(UserAuthAdapter::class);
        
        $container->get(ProductFavoritesRepositoryInterface::class);
        $container->get(ProductHistoryRepositoryInterface::class);
        
        $mclient = new \MongoDB\Client(
            'mongodb://saychas:saychas@localhost/saychas'
        );

        return new MyTestController($test, $category, $provider, $store, $providerRelatedStore, $product, $filteredProduct, $brand, $characteristic, $characteristicValue,
                $price, $stockBalance, $handBookProduct, $userRepository, $entityManager, $config, $htmlProvider, $htmlFormProvider, $authService, $adapter, $userAdapter, $mclient
                /**,
                $productFavorites, $productHistory*/);
    }

}
