<?php

// ControlPanel/src/Module.php

namespace ControlPanel;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use ControlPanel\Listener\LayoutListener;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Resolver\TemplateMapResolver;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Json\Json;
use ControlPanel\Controller\AuthController;
use ControlPanel\Service\AuthManager;

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
        
        $authManager = $event->getApplication()->getServiceManager()->get(AuthManager::class);
        
        if ($controllerName != AuthController::class &&
            $controllerName != \Application\Controller\IndexController::class &&
            $controllerName != \Application\Controller\UserDataController::class &&
            $controllerName != \Application\Controller\AjaxController::class &&
            $controllerName != \Application\Controller\ReceivingController::class &&
            $controllerName != \Application\Controller\FtpController::class &&
            $controllerName != \Application\Controller\MyTestController::class &&
            $controllerName != \Application\Controller\AcquiringController::class) {
            
            $hasIdentity = $authManager->hasIdentity();
            if(!$hasIdentity) {
                $request = $event->getApplication()->getRequest();
                if($request->isXmlHttpRequest()) {
                    $data = Json::encode(['data' => true]);//  json_encode(['data' => false]); // 
                    //return $controller->redirect()->toUrl('/control-panel/login?data='.$data);
                    return $controller->redirect()->toUrl('/control-panel/provider-login?data='.$data);
                }
//                $controller->layout()->setTemplate('layout/control-panel-auth');
//                return $controller->redirect()->toUrl('/control-panel/login');
            }

            $result = $authManager->filterAccess($controllerName, $actionName);
            
            if($result == AuthManager::AUTH_REQUIRED) {
                $uri = $event->getApplication()->getRequest()->getUri();
                $uri->setScheme(null)
                    ->setHost(null)
                    ->setPort(null)
                    ->setUserInfo(null)
                    ->setPath('/control-panel/provider-login');
                    //->setPath('/control-panel/login');
//                $redirectUrl = $uri->toString();
                $redirectUrl = $uri->toString();
//                $query = $uri->getQuery();
                /** temporarily comment the following line out */
                //$redirectUrl = '/control-panel/login?'.$query;
//                $r = $controller->redirect()->toRoute('control-panel/login', [], ['query'=>$query]);
//                $redirectUrl = $r->toString();
                return $controller->redirect()->toUrl($redirectUrl);
            }else if ($result==AuthManager::ACCESS_DENIED) {
                // Redirect the user to the "Not Authorized" page.
                return $controller->redirect()->toRoute('control-panel/not-authorized');
            }


//        $returnUrl = $this->params()->fromQuery('returnUrl');
//        return new ViewModel(['action' => '/control-panel/check-login', 'returnUrl' => $returnUrl]);
            // Redirect the user to the "Login" page.
//            return $controller->redirect()->toRoute('control-panel/login', [], 
//                ['query'=>['redirectUrl'=>$redirectUrl]]);
//            return $this->redirect()->toUrl($post['returnUrl']);
        }
        
    }
}
