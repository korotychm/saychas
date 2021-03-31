<?php
// Application\src\Service\Factory\HtmlProviderServiceFactory.php

namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Application\Service\HtmlProviderService;
//use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class HtmlProviderServiceFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof HtmlProviderService){
            throw new Exception("not instanceof HtmlProviderService");
        }
        
//        $adapter = $container->get(AdapterInterface::class);
        
        return new HtmlProviderService(
        );
    }
}