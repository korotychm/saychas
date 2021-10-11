<?php

// ControlPanel/src/Controller/Plugin/Factory/ImagePathPluginFactory.php

namespace ControlPanel\Controller\Plugin\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ControlPanel\Controller\Plugin\ImagePathPlugin;

/**
 * This is the factory for AccessPlugin. Its purpose is to instantiate the plugin
 * and inject dependencies into its constructor.
 */
class ImagePathPluginFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $imagePath = $config['parameters']['image_path'];
        
        return new ImagePathPlugin($imagePath);
    }

}
