<?php

// ControlPanel\src\Service\Factory\HtmlContentProviderFactory.php

namespace ControlPanel\Service\Factory;

use Interop\Container\ContainerInterface;
use ControlPanel\Service\HtmlContentProvider;
use Laminas\ServiceManager\Factory\FactoryInterface;

class HtmlContentProviderFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof HtmlContentProvider) {
            throw new Exception("not instanceof HtmlContentProvider");
        }

        return new HtmlContentProvider($container);
    }

}
