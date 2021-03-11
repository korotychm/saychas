<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
//use Laminas\ServiceManager\Factory\InvokableFactory;
//use Laminas\Db\Adapter\AdapterAbstractServiceFactory;
use Application\Controller\Factory\IndexControllerFactory;
use Application\Controller\Factory\ReceivingControllerFactory;
use Laminas\Db\Adapter\AdapterAbstractServiceFactory;
//use Laminas\ServiceManager\Factory\InvokableFactory;
//use Application\Model\Factory\LaminasDbSqlRepositoryFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'preview' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/preview',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'preview',
                    ],
                ],
            ],
            'show-provider' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/show-provider[/:id]',
                    'defaults' => [
                        'controller' => Controller\ReceivingController::class,
                        'action'     => 'showProvider',
                    ],
                ],
            ],
//            'receive-provider' => [
//                'type'    => Segment::class,
//                'options' => [
//                    'route'    => '/receive-provider[/:id]',
//                    'defaults' => [
//                        'controller' => Controller\ReceivingController::class,
//                        'action'     => 'receiveProvider',
//                    ],
//                ],
//            ],
            'receive' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/receive[/:id]',
                    'defaults' => [
                        'controller' => Controller\ReceivingController::class,
                        'action'     => 'receiveProvider',
                    ],
                ],
            ],
            'ajax' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/ajax[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'ajax',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
//            Controller\IndexController::class => InvokableFactory::class,
            Controller\IndexController::class => IndexControllerFactory::class,
            Controller\ReceivingController::class => ReceivingControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'aliases' => [
            //\Application\Model\TestRepositoryInterface::class => \Application\Model\TestRepository::class,
            \Application\Model\TestRepositoryInterface::class => \Application\Model\LaminasDbSqlRepository::class,
            \Application\Model\CategoryRepositoryInterface::class => \Application\Model\CategoryRepository::class,
            \Application\Model\ProviderRepositoryInterface::class => \Application\Model\ProviderRepository::class,
        ],
        'factories' => [
            //'Application\Db\WriteAdapter' => AdapterAbstractServiceFactory::class,
            //\Application\Model\TestRepository::class => InvokableFactory::class,
            'Application\Db\WriteAdapter' => AdapterAbstractServiceFactory::class,
            \Laminas\Db\Adapter\AdapterInterface::class => \Laminas\Db\Adapter\Adapter::class,
            \Application\Model\LaminasDbSqlRepository::class => \Application\Model\Factory\LaminasDbSqlRepositoryFactory::class,
            \Application\Model\CategoryRepository::class => \Application\Model\Factory\CategoryRepositoryFactory::class,
            \Application\Model\ProviderRepository::class => \Application\Model\Factory\ProviderRepositoryFactory::class,
        ],
        
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'layout/preview'           => __DIR__ . '/../view/layout/menu.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'parameters' => [
        '1c_auth' => [
            'username' => 'administrator',
            'password' => 'w48Es4562',
        ],
    ],
//    'driver' => [
//        __NAMESPACE__ . '_driver' => [
//            'class' => AnnotationDriver::class,
//            'cache' => 'array',
//            'paths' => [__DIR__ . '/../src/Entity']
//        ],
//        'orm_default' => [
//            'drivers' => [
//                __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
//            ],
//        ],
//    ],
];
