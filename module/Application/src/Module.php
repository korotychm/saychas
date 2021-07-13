<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace Application;

use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\SessionManager;
use Laminas\Session\Config\SessionConfig;
use Laminas\Session\Container;
use Laminas\Session\Validator;

//use Application\ConfigProvider;

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

    public function onBootstrap(MvcEvent $e)
    {
//        $eventManager = $e->getApplication()->getEventManager();
//        $moduleRouteListener = new ModuleRouteListener();
//        $moduleRouteListener->attach($eventManager);
//        $this->bootstrapSession($e);
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
