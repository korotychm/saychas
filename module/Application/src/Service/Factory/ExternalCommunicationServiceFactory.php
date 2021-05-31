<?php
// Application\src\Service\Factory\ExternalCommunicationServiceFactory.php

namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Service\ExternalCommunicationService;

class ExternalCommunicationServiceFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof ExternalCommunicationService){
            throw new Exception("not instanceof ExternalCommunicationService");
        }
        
        $config = $container->get('Config');
        
        return new ExternalCommunicationService($config);
    }
}