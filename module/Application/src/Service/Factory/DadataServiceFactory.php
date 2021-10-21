<?php

// Application\src\Service\Factory\DadataServiceFactory.php

namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Service\DadataService;

//use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;

class DadataServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof DadataService) {
            throw new Exception("not instanceof DadataService");
        }

        $config = $container->get('Config');
        
    
        return new DadataService($config, $url, $token);
    }

}
