<?php

// ControlPanel/src/Module.php

namespace ControlPanel;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use ControlPanel\Listener\LayoutListener;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Resolver\TemplateMapResolver;

/**
 * Description of Module
 *
 * @author alex
 */
class Module implements ConfigProviderInterface
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $event): void
    {
        $application = $event->getApplication();

        /** @var TemplateMapResolver $templateMapResolver */
        $templateMapResolver = $application->getServiceManager()->get(
                'ViewTemplateMapResolver'
        );

        // Create and register layout listener
        $listener = new LayoutListener($templateMapResolver);
        $listener->attach($application->getEventManager());
    }

}
