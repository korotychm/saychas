<?php

// ControlPanel/src/Controller/Plugin/Factory/DocumentPathPluginFactory.php

namespace ControlPanel\Controller\Plugin\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ControlPanel\Controller\Plugin\DocumentPathPlugin;

/**
 * This is the factory for AccessPlugin. Its purpose is to instantiate the plugin
 * and inject dependencies into its constructor.
 */
class DocumentPathPluginFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $path = $config['parameters']['document_path'];
        
        return new DocumentPathPlugin($path);
    }

}
