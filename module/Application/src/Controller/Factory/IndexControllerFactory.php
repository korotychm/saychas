<?php
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
use Application\Service\HtmlProviderService;
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
        $test = $container->get(/**\Application\Model\*/TestRepositoryInterface::class);
        $category = $container->get(CategoryRepositoryInterface::class);
        $provider = $container->get(ProviderRepositoryInterface::class);
        $store =    $container->get(StoreRepositoryInterface::class);
        $product =    $container->get(ProductRepositoryInterface::class);
        $filteredProduct = $container->get(FilteredProductRepositoryInterface::class);
        $brand =    $container->get(BrandRepositoryInterface::class);
        $characteristic =    $container->get(CharacteristicRepositoryInterface::class);
        $price =    $container->get(PriceRepositoryInterface::class);
        $stockBalance =    $container->get(StockBalanceRepositoryInterface::class);
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $config = $container->get('Config');
        $htmlProvider = $container->get(HtmlProviderService::class);
        $htmlFormProvider = $container->get(HtmlFormProviderService::class);
        return new IndexController($test, $category, $provider, $store, $product, $filteredProduct, $brand, $characteristic,
                $price, $stockBalance, $entityManager, $config, $htmlProvider, $htmlFormProvider);
    }
}

