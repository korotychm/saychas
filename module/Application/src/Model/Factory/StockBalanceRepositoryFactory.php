<?php
// src/Model/Factory/PriceRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\StockBalance;
use Application\Model\Repository\StockBalanceRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class StockBalanceRepositoryFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof StockBalanceRepository){
            throw new Exception("not instanceof StockBalanceRepository");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        
        return new StockBalanceRepository(
            $adapter,
            new ReflectionHydrator(),
            new StockBalance('', 0, '')
        );
    }
}