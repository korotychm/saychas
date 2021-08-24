<?php

// Application\src\Service\Factory\AcquiringCommunicationServiceFactory.php

namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Service\AcquiringCommunicationService;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;

class AcquiringCommunicationServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof AcquiringCommunicationService) {
            throw new Exception("not instanceof AcquiringCommunicationService");
        }

        $config = $container->get('Config');
        $productRepository = $container->get(HandbookRelatedProductRepositoryInterface::class);

        return new AcquiringCommunicationService($config, $productRepository);
    }

}
