<?php
// Application\src\Service\Factory\HtmlProviderServiceFactory.php

namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Application\Service\HtmlProviderService;
//use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Application\Model\RepositoryInterface\BrandRepositoryInterface;
use Application\Model\RepositoryInterface\ColorRepositoryInterface;
use Application\Model\RepositoryInterface\CountryRepositoryInterface;
use Application\Model\RepositoryInterface\ProviderRepositoryInterface;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\ProductCharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicValueRepositoryInterface;

class HtmlProviderServiceFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof HtmlProviderService){
            throw new Exception("not instanceof HtmlProviderService");
        }
        
//        $adapter = $container->get(AdapterInterface::class);
        $stockBalanceRepository = $container->get(StockBalanceRepositoryInterface::class);
        $brandRepository = $container->get(BrandRepositoryInterface::class);
        $colorRepository = $container->get(ColorRepositoryInterface::class);
        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $providerRepository = $container->get(ProviderRepositoryInterface::class);
        $priceRepository = $container->get(PriceRepositoryInterface::class);
        $characteristicRepository = $container->get(CharacteristicRepositoryInterface::class);
        $productCharacteristicRepository = $container->get(ProductCharacteristicRepositoryInterface::class);
        $characteristicValueRepository = $container->get(CharacteristicValueRepositoryInterface::class);
        
        return new HtmlProviderService($stockBalanceRepository, $brandRepository, $colorRepository, $countryRepository, $providerRepository, $priceRepository,
                $characteristicRepository, $productCharacteristicRepository, $characteristicValueRepository);
    }
}