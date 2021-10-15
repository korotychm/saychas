<?php

// General/src/Module.php

namespace General;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;
//use ControlPanel\Listener\LayoutListener;
//use Laminas\Mvc\MvcEvent;
//use Laminas\View\Resolver\TemplateMapResolver;
//use Laminas\Mvc\Controller\AbstractActionController;
//use Laminas\Json\Json;
//use ControlPanel\Controller\AuthController;
//use ControlPanel\Service\AuthManager;

/**
 * Description of Module
 *
 * @author alex
 */
class Module implements ConfigProviderInterface
{

    public function getConfig()
    {
        return [];// include __DIR__ . '/../config/module.config.php';        
    }
}
