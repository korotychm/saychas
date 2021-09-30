<?php

// ControlPanel\src\Service\Factory\StoreManagerFactory.php

namespace ControlPanel\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ControlPanel\Service\CurlRequestManager;
//use Application\Service\ExternalCommunicationService;
use ControlPanel\Service\StoreManager;

/**
 * Description of StoreManagerFactory
 *
 * @author alex
 */
class StoreManagerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof StoreManager) {
            throw new Exception("not instanceof EntityManager");
        }

        $config = $container->get('Config');
        $curlRequestManager = $container->get(CurlRequestManager::class);
        $mclient = new \MongoDB\Client(
            'mongodb://saychas_cache:saychas_cache@localhost/saychas_cache'
        );

        return new StoreManager($config, $curlRequestManager, $mclient);
    }

}
