<?php

// src/Model/Factory/HandbookRelatedProductRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\HandbookRelatedProduct;
use Application\Model\Repository\HandbookRelatedProductRepository;
use Application\Model\Repository\BrandRepository;
use Application\Model\Repository\PriceRepository;
use Application\Model\Repository\ProductImageRepository;
use Application\Model\Repository\ProviderRepository;
use Application\Model\Repository\ProductCharacteristicRepository;
use Application\Model\Repository\StockBalanceRepository;
use Application\Model\Repository\MarkerRepository;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class HandbookRelatedProductRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof HandbookRelatedProductRepository) {
            throw new Exception("not instanceof HandbookRelatedProductRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        $brandRepository = $container->get(BrandRepository::class);
        $priceRepository = $container->get(PriceRepository::class);
        $productImageRepository = $container->get(ProductImageRepository::class);
        $providerRepository = $container->get(ProviderRepository::class);
        $productCharacteristicRepository = $container->get(ProductCharacteristicRepository::class);
        $stockBalanceRepository = $container->get(StockBalanceRepository::class);
        $markerRepository = $container->get(MarkerRepository::class);

        $prototype = new HandbookRelatedProduct;
        $prototype::$brandRepository = $brandRepository;
        $prototype::$priceRepository = $priceRepository;
        $prototype::$productImageRepository = $productImageRepository;
        $prototype::$providerRepository = $providerRepository;
        $prototype::$productCharacteristicRepository = $productCharacteristicRepository;
        $prototype::$stockBalanceRepository = $stockBalanceRepository;
        $prototype::$markerRepository = $markerRepository;

        $hydrator = new ClassMethodsHydrator();
        
        $composite = new \Laminas\Hydrator\Filter\FilterComposite();
        $composite->addFilter(
                'excludeval',
                new \Laminas\Hydrator\Filter\MethodMatchFilter('getProvider'),
                \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND
        );

        $hydrator->addFilter('excludes', $composite, \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND);
        
        return new HandbookRelatedProductRepository(
                $adapter,
                $hydrator, // new ClassMethodsHydrator(),
                $prototype
        );
    }

}
