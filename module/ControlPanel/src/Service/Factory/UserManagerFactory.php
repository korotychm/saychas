<?php

// ControlPanel\src\Service\Factory\UserManagerFactory.php

namespace ControlPanel\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ControlPanel\Service\CurlRequestManager;
//use Application\Service\ExternalCommunicationService;
use ControlPanel\Service\UserManager;

/**
 * Description of UserManagerFactory
 *
 * @author alex
 */
class UserManagerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof UserManager) {
            throw new Exception("not instanceof EntityManager");
        }
        
        $config = $container->get('Config');
        $curlRequestManager = $container->get(CurlRequestManager::class);
        
        return new UserManager($config, $curlRequestManager);
    }

}
