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
    public function getConfig() : array
    {
//        $v = include __DIR__ . '/../config/module.config.php';
//        echo '<pre>';
//        print_r($v);
//        echo '</pre>';
//        exit;
//        $configProvider = new \Application\ConfigProvider();
//
//        $conf = [
//            'laminas-cli' => $configProvider->getCliConfig(),
//            'service_manager' => $configProvider->getDependencyConfig(),
//        ];
        
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
//        $eventManager        = $e->getApplication()->getEventManager();
//        $moduleRouteListener = new ModuleRouteListener();
//        $moduleRouteListener->attach($eventManager);
//        $this->bootstrapSession($e);
    }
}
