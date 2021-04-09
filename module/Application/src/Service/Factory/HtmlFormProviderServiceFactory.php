<?php
// Application\src\Service\Factory\HtmlProviderServiceFactory.php

namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Application\Service\HtmlFormProviderService;
//use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;

class HtmlFormProviderServiceFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof HtmlFormProviderService){
            throw new Exception("not instanceof HtmlFormProviderService");
        }
        
//        $adapter = $container->get(AdapterInterface::class);
        $stockBalanceRepository = $container->get(StockBalanceRepositoryInterface::class);
        
        return new HtmlFormProviderService($stockBalanceRepository);
    }
}