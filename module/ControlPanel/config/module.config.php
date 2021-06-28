<?php

namespace ControlPanel;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
//use Laminas\Router\Http\Hostname;
//use Laminas\ServiceManager\Factory\InvokableFactory;
//use ControlPanel\Controller\IndexController;
use ControlPanel\Controller\Factory\IndexControllerFactory;
use ControlPanel\Service\Factory\HtmlContentProviderFactory;

return [
    'controllers' => [
        'factories' => [
//            \ControlPanel\Controller\IndexController::class => InvokableFactory::class,
            \ControlPanel\Controller\IndexController::class => IndexControllerFactory::class,
        ],
    'controllers' => [
        'factories' => [
//            Controller\IndexController::class => InvokableFactory::class,
            Controller\IndexController::class => IndexControllerFactory::class,
        ],
    ],
        
    ],
    'router' => [
        'routes' => [
            'control-panel' =>  [
                'type' => Literal::class,
                'options' => [
                    'route' => '/control-panel',
                    'defaults' => [
                        'controller' => \ControlPanel\Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'show-stores' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/show-stores',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\IndexController::class,
                                'action' => 'show-stores',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
                    'show-one-store' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/show-one-store[/:id]',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\IndexController::class,
                                'action' => 'show-one-store',
                            ],
                        ],
                    ],
                    'show-products' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/show-products',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\IndexController::class,
                                'action' => 'show-products',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
                ],
            ],
//            'control-panel' => [
//                'type'    => Segment::class,
//                'options' => [
////                    'route' => '/control-panel[/:action[/:id]]',
//                    'route' => '/control-panel[/:action]',
//                    'constraints' => [
//                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
////                        'id'     => '[0-9]+',
//                    ],
//                    'defaults' => [
//                        'controller' => \ControlPanel\Controller\IndexController::class,
//                        'action'     => 'index',
//                    ],
//                ],
//                
//            ],
//            'lk.saychas.com' => [
//                'type' => Hostname::class,
//                'options' => [
//                    'route' => ':3rd.:2nd.:1st',
//                    'constraints' => [
//                        ':3rd' => 'lk',
//                        ':2nd' => 'saychas-z',
//                        ':1st' => 'local',
//                    ],
//                ],
////                'may_terminate' => true,
//                'child_routes' => [
//                    'index' => [
//                        'type' => Literal::class,
//                        'options' => [
//                            'route' => '/',
//                            'defaults' => [
//                                'controller' => \ControlPanel\Controller\IndexController::class,
//                                'action' => 'index',
//                            ],
//                        ],
//                        'may_terminate' => true,
//                    ],
//                ],
//            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            \ControlPanel\Service\HtmlContentProvider::class => \ControlPanel\Service\Factory\HtmlContentProviderFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'control-panel' => __DIR__ . '/../view',
        ],
        'template_map' => [
            'layout/control-panel'           => __DIR__ . '/../view/layout/control-panel.phtml',
            
            'control-panel/index/index' => __DIR__ . '/../view/control-panel/index/index.phtml',
//            'error/404'               => __DIR__ . '/../view/error/404.phtml',
//            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'strategies' => ['ViewJsonStrategy',],
    ],
    
];