<?php

// ControlPanel\src\Service\Factory\ProductManagerFactory.php

namespace ControlPanel\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ControlPanel\Service\CurlRequestManager;
//use Application\Service\ExternalCommunicationService;
use ControlPanel\Service\ProductManager;
use Application\Model\Repository\CategoryRepository;

/**
 * Description of ProductManagerFactory
 *
 * @author alex
 */
class ProductManagerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof ProductManager) {
            throw new Exception("not instanceof EntityManager");
        }
        
        $config = $container->get('Config');
        $curlRequestManager = $container->get(CurlRequestManager::class);
        $mclient = new \MongoDB\Client(
            'mongodb://saychas_cache:saychas_cache@localhost/saychas_cache'
        );
        $categoryRepo = $container->get(CategoryRepository::class);
        $entityManager = $container->get('laminas.entity.manager');
        
        
        return new ProductManager($config, $curlRequestManager, $mclient, $entityManager, $categoryRepo);
    }

}
