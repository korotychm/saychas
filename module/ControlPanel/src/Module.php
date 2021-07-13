<?php

// ControlPanel/src/Module.php

namespace ControlPanel;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use ControlPanel\Listener\LayoutListener;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Resolver\TemplateMapResolver;
use Laminas\Mvc\Controller\AbstractActionController;
use ControlPanel\Controller\LoginController;

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
        
        $eventManager = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
//        // Register the event listener method. 
        $sharedEventManager->attach(AbstractActionController::class, 
                MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
        
        //$sessionManager = $e->getApplication()->getServiceManager()->get('Zend\Session\SessionManager');
        
    }
    
    public function onDispatch(MvcEvent $event)
    {
        $controller = $event->getTarget();
        $controllerName = $event->getRouteMatch()->getParam('controller', null);
        $actionName = $event->getRouteMatch()->getParam('action', null);
        $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));
        
        if ($controllerName != LoginController::class) {
//            $uri = $event->getApplication()->getRequest()->getUri();
//            $uri->setScheme(null)
//                ->setHost(null)
//                ->setPort(null)
//                ->setUserInfo(null);
//            $redirectUrl = $uri->toString();
//
//            // Redirect the user to the "Login" page.
//            return $controller->redirect()->toRoute('control-panel/login', [], 
//                ['query'=>['redirectUrl'=>$redirectUrl]]);
        }
        
    }
}
