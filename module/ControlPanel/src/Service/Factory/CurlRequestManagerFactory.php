<?php

// ControlPanel\src\Service\Factory\CurlRequestManagerFactory.php

namespace ControlPanel\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ControlPanel\Service\CurlRequestManager;

/**
 * Description of CurlRequestManagerFactory
 *
 * @author alex
 */
class CurlRequestManagerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof CurlRequestManager) {
            throw new Exception("not instanceof CurlRequestManager");
        }
        
        $config = $container->get('Config');
        
        return new CurlRequestManager($config);
    }

}
