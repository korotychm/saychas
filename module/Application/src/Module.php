<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace Application;

//use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;
//use Laminas\Session\SessionManager;
//use Laminas\Session\Config\SessionConfig;
//use Laminas\Session\Container;
//use Laminas\Session\Validator;

use Laminas\Authentication\AuthenticationService;
use Application\Adapter\Auth\UserAuthAdapter;
//use Application\Model\Entity\User;
use Laminas\View\Resolver\TemplateMapResolver;
//use Application\Model\Entity\HandbookRelatedProduct;
//use Application\Service\CommonHelperFunctionsService;
use Application\Resource\Resource;
//use Laminas\Mvc\Controller\AbstractActionController;

use Laminas\EventManager\EventInterface as Event;
use Laminas\ModuleManager\ModuleManager;

//use ControlPanel\Listener\LayoutListener;
//use Laminas\Mvc\Controller\AbstractActionController;

//use Application\ConfigProvider;

use Application\Model\RepositoryInterface\CategoryRepositoryInterface;

class Module
{

    public function getConfig(): array
    {
//        $configProvider = new \Application\ConfigProvider();

        return include __DIR__ . '/../config/module.config.php';
        //return array_merge( $conf, include __DIR__ . '/../config/module.config.php' );
    }

//    public function getViewHelperConfig()
//    {
//        return array(
//           'invokables' => array(
//              'catalog' => 'Application\View\Helper\CatalogHelper',
//           ),
//        );
//   }
    
    /**
    public function init(ModuleManager $moduleManager)
    {
        // Remember to keep the init() method as lightweight as possible
        $events = $moduleManager->getEventManager();
        $events->attach('loadModules.post', [$this, 'modulesLoaded']);
    }
    
    public function modulesLoaded(Event $e)
    {
        // This method is called once all modules are loaded.
        $moduleManager = $e->getTarget();
        $name = $e->getName();
        $loadedModules = $moduleManager->getLoadedModules();

        // To get the configuration from another module named 'FooModule'
        $config = $moduleManager->getModule('Application')->getConfig();
    }
    */
    
    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();

        $eventManager = $app->getEventManager();
        
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this,'onDispatch'), 100);
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array($this,'onDispatch'), 100);
        
    }
    
    public function onDispatch(MvcEvent $e)
    {

        $viewModel = $e->getViewModel();
        $viewModel->setTemplate('layout/layout');
        
        //$controller = $e->getTarget();

        $userRepository = $e->getApplication()->getServiceManager()->get(\Application\Model\Repository\UserRepository::class);
        $userAuthAdapter = new UserAuthAdapter($userRepository);
        $authService = $e->getApplication()->getServiceManager()->get(AuthenticationService::class);
        $result = $authService->authenticate($userAuthAdapter);
        //$code = $result->getCode();
        $userId = $result->getIdentity();
        
        $categoryRepository = $e->getApplication()->getServiceManager()->get(\Application\Model\RepositoryInterface\CategoryRepositoryInterface::class);
        $commonHelperFuncions = $e->getApplication()->getServiceManager()->get(\Application\Service\CommonHelperFunctionsService::class);
        $htmlProvider = $e->getApplication()->getServiceManager()->get(\Application\Service\HtmlProviderService::class);
        $setting = $e->getApplication()->getServiceManager()->get(\Application\Model\Entity\Setting::class);

        $user = $userRepository->find(['id' => $userId]);
        $userInfo = $commonHelperFuncions->getUserInfo($user);
        $mainMenu = (!empty($mainMenu = $setting->find(['id' => 'main_menu']))) ? $mainMenu = $htmlProvider->getMainMenu($mainMenu) : [];
        $addressLegal = $userInfo["userAddress"] ??  false;
        $userLegal = ($userInfo["userid"] and $userInfo["phone"]) ? true : false;
        $userAddressArray = $htmlProvider->getUserAddresses($user, Resource::LIMIT_USER_ADDRESS_LIST);

        $viewModel->setVariables([
            'categoryTree' => $categoryRepository->categoryFilteredTree(),
            'addressLegal' => $addressLegal,
            'addresses' => $userAddressArray,
            'addressesJson' => json_encode($userAddressArray, JSON_UNESCAPED_UNICODE),
            'userLegal' => $userLegal,
            'userinfo' => $userInfo,
            'mainMenu' => $mainMenu,
            'basketProductsCount' => $commonHelperFuncions->basketProductsCount($userId),
        ]);
      
    }


//    public function bootstrapSession(MvcEvent $e)
//    {
//        $session = $e->getApplication()
//                ->getServiceManager()
//                ->get(SessionManager::class);
//        $session->start();
//
//        $container = new Container('initialized');
//
//        if (isset($container->init)) {
//            return;
//        }
//
//        $serviceManager = $e->getApplication()->getServiceManager();
//        $request = $serviceManager->get('Request');
//
//        $session->regenerateId(true);
//        $container->init = 1;
//        $container->remoteAddr = $request->getServer()->get('REMOTE_ADDR');
//        $container->httpUserAgent = $request->getServer()->get('HTTP_USER_AGENT');
//
//        $config = $serviceManager->get('Config');
//        if (!isset($config['session'])) {
//            return;
//        }
//
//        $sessionConfig = $config['session'];
//
//        if (!isset($sessionConfig['validators'])) {
//            return;
//        }
//
//        $chain = $session->getValidatorChain();
//
//        foreach ($sessionConfig['validators'] as $validator) {
//            switch ($validator) {
//                case Validator\HttpUserAgent::class:
//                    $validator = new $validator($container->httpUserAgent);
//                    break;
//                case Validator\RemoteAddr::class:
//                    $validator = new $validator($container->remoteAddr);
//                    break;
//                default:
//                    $validator = new $validator();
//                    break;
//            }
//
//            $chain->attach('session.validate', array($validator, 'isValid'));
//        }
//    }

//    public function getServiceConfig()
//    {
//        return [
//            'factories' => [
//                SessionManager::class => function ($container) {
//                    $config = $container->get('config');
//                    if (!isset($config['session'])) {
//                        $sessionManager = new SessionManager();
//                        Container::setDefaultManager($sessionManager);
//                        return $sessionManager;
//                    }
//
//                    $session = $config['session'];
//
//                    $sessionConfig = null;
//                    if (isset($session['config'])) {
//                        $class = isset($session['config']['class']) ? $session['config']['class'] : SessionConfig::class;
//
//                        $options = isset($session['config']['options']) ? $session['config']['options'] : [];
//
//                        $sessionConfig = new $class();
//                        $sessionConfig->setOptions($options);
//                    }
//
//                    $sessionStorage = null;
//                    if (isset($session['storage'])) {
//                        $class = $session['storage'];
//                        $sessionStorage = new $class();
//                    }
//
//                    $sessionSaveHandler = null;
//                    if (isset($session['save_handler'])) {
//                        // class should be fetched from service manager
//                        // since it will require constructor arguments
//                        $sessionSaveHandler = $container->get($session['save_handler']);
//                    }
//
//                    $sessionManager = new SessionManager(
//                            $sessionConfig,
//                            $sessionStorage,
//                            $sessionSaveHandler
//                    );
//
//                    Container::setDefaultManager($sessionManager);
//                    return $sessionManager;
//                },
//            ],
//        ];
//    }

}










/** @var TemplateMapResolver $templateMapResolver */
//        $templateMapResolver = $app->getServiceManager()->get(
//                'ViewTemplateMapResolver'
//        );
//
////        // Create and register layout listener
//        $listener = new LayoutListener($templateMapResolver);
//        $listener->attach($app->getEventManager());

//        $serviceManager = $app->getServiceManager();
//        $sharedManager = $app->getEventManager()->getSharedManager();        








//        $eventManager = $event->getApplication()->getEventManager();
//        $sharedEventManager = $eventManager->getSharedManager();
//        // Register the event listener method. 
//        $sharedEventManager->attach(AbstractActionController::class, 
//                MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
        
//        $eventManager = $e->getApplication()->getEventManager();
//        $moduleRouteListener = new ModuleRouteListener();
//        $moduleRouteListener->attach($eventManager);
//        $this->bootstrapSession($e);
        

//        $controllerName = $e->getRouteMatch()->getParam('controller', null);
//        $routeMatch = $e->getRouteMatch();
//        //$matchedRouteName = $routeMatch->getMatchedRouteName();
//        $routeUrl = $e->getRouteMatch()->getParam('url', null);
//        $actionName = $e->getRouteMatch()->getParam('action', null);
//        $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));
        
        //$authManager = $e->getApplication()->getServiceManager()->get(AuthManager::class);
        
//        $container = new Container();
//        $signUp = $container->signUp;

