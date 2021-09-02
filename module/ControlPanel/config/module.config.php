<?php

namespace ControlPanel;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
//use Laminas\Router\Http\Hostname;
//use Laminas\ServiceManager\Factory\InvokableFactory;
use ControlPanel\Controller\Factory\IndexControllerFactory;
use ControlPanel\Controller\Factory\AuthControllerFactory;
use ControlPanel\Controller\Factory\ProductControllerFactory;
use ControlPanel\Controller\Factory\StoreControllerFactory;
//use ControlPanel\Controller\Factory\StandardControllerFactory;

return [
    'controllers' => [
        'factories' => [
//            \ControlPanel\Controller\IndexController::class => StandardControllerFactory::class,
//            \ControlPanel\Controller\AuthController::class => StandardControllerFactory::class,
            \ControlPanel\Controller\IndexController::class => IndexControllerFactory::class,
            \ControlPanel\Controller\AuthController::class => AuthControllerFactory::class,
            \ControlPanel\Controller\ProductController::class => ProductControllerFactory::class,
            \ControlPanel\Controller\StoreController::class => StoreControllerFactory::class,
        ],        
    ],
    // The 'access_filter' key is used by the User module to restrict or permit
    // access to certain controller actions for unauthorized visitors.
    'access_filter' => [
        'controllers' => [
//            Controller\UserController::class => [
//                // Give access to "resetPassword", "message" and "setPassword" actions
//                // to anyone.
//                ['actions' => ['resetPassword', 'message', 'setPassword'], 'allow' => '*'],
//                // Give access to "index", "add", "edit", "view", "changePassword" actions to users having the "user.manage" permission.
//                ['actions' => ['index', 'add', 'edit', 'view', 'changePassword'], 'allow' => '+user.manage']
//            ],
//            Controller\RoleController::class => [
//                // Allow access to authenticated users having the "role.manage" permission.
//                ['actions' => '*', 'allow' => '+role.manage']
//            ],
//            Controller\PermissionController::class => [
//                // Allow access to authenticated users having "permission.manage" permission.
//                ['actions' => '*', 'allow' => '+permission.manage']
//            ],
            
    //indexAction
    //showStoresAction
    //showOneStoreAction
    //showProductsAction
    //profileAction
    //actionAndDiscountAction
    //accountManagementAction
    //respondingToReviewsAction
    //calendarDetailsAction
            
            
            \ControlPanel\Controller\IndexController::class => [
                ['actions' => ['index'/*, 'showStores'*/, 'showOneStore', 'showProducts', 'profile', //  'userManagement',
                    /*'actionAndDiscount', 'accountManagement', 'respondingToReviews', 'calendarDetails',*/ ], 'allow' => '*'],
                ['actions' => ['actionAndDiscount',], 'allow' => '+analyst'],
                ['actions' => ['userManagement',], 'allow' => '@Banzaii'],
                ['actions' => ['showStores',], 'allow' => '+analyst'],
            ],
            \ControlPanel\Controller\ProductController::class => [
                ['actions' => ['showProducts', ], 'allow' => '+developer'],
                ['actions' => ['showProductsFromCache', ], 'allow' => '+developer'],
            ],
        ]
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
                    'empty' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\IndexController::class,
                                'action' => 'index',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
                    'index' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/index',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\IndexController::class,
                                'action' => 'index',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
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
                                'controller' => \ControlPanel\Controller\ProductController::class,
                                'action' => 'show-products',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
                    'show-products-from-cache' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/show-products-from-cache',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\ProductController::class,
                                'action' => 'show-products-from-cache',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
                    'profile' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/profile',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\IndexController::class,
                                'action' => 'profile',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
                    'user-management' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/user-management',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\IndexController::class,
                                'action' => 'user-management',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
                    'account-management' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/account-management',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\IndexController::class,
                                'action' => 'account-management',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
                    'action-and-discount' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/action-and-discount',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\IndexController::class,
                                'action' => 'action-and-discount',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
                    'responding-to-reviews' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/responding-to-reviews',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\IndexController::class,
                                'action' => 'responding-to-reviews',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
                    'calendar-details' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/calendar-details[/:id]',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\IndexController::class,
                                'action' => 'calendar-details',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
//                    'login-form' => [
//                        'type' => Literal::class,
//                        'options' => [
//                            'route' => '/login-form',
//                            'defaults' => [
//                                'controller' => \ControlPanel\Controller\IndexController::class,
//                                'action' => 'login-form',
//                            ],
//                        ],
//                        // 'may_terminate' => true,
//                    ],
                    'login' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/login',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\AuthController::class,
                                'action' => 'login',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
                    'provider-login' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/provider-login',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\AuthController::class,
                                'action' => 'provider-login',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
                    'check-login' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/check-login',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\AuthController::class,
                                'action' => 'check-login',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
                    'check-provider-login' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/check-provider-login',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\AuthController::class,
                                'action' => 'check-provider-login',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
                    'logout' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/logout',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\AuthController::class,
                                'action' => 'logout',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
                    'not-authorized' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/not-authorized',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\AuthController::class,
                                'action' => 'not-authorized',
                            ],
                        ],
                        // 'may_terminate' => true,
                    ],
                    'not-authorized-view' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/not-authorized-view',
                            'defaults' => [
                                'controller' => \ControlPanel\Controller\AuthController::class,
                                'action' => 'not-authorized-view',
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
    'rbac_manager' => [
        'assertions' => [\ControlPanel\Service\RbacAssertionManager::class],
    ],
    'service_manager' => [
        'factories' => [
            \ControlPanel\Service\HtmlContentProvider::class => \ControlPanel\Service\Factory\HtmlContentProviderFactory::class,
            \ControlPanel\Service\RbacAssertionManager::class => \ControlPanel\Service\Factory\RbacAssertionManagerFactory::class,
            \ControlPanel\Service\RbacManager::class => \ControlPanel\Service\Factory\RbacManagerFactory::class,
            
            /** Entity manager */
            'laminas.entity.manager' => \ControlPanel\Service\Factory\EntityManagerFactory::class,            
            /** Entities */
            \ControlPanel\Model\Entity\Role::class => \ControlPanel\Model\Repository\Factory\RoleRepositoryFactory::class,
            \ControlPanel\Model\Entity\RoleHierarchy::class => \ControlPanel\Model\Repository\Factory\RoleHierarchyRepositoryFactory::class,
            /** Curl Request Manager */
            \ControlPanel\Service\CurlRequestManager::class => \ControlPanel\Service\Factory\CurlRequestManagerFactory::class,
            /** User Manager */
            \ControlPanel\Service\UserManager::class => \ControlPanel\Service\Factory\UserManagerFactory::class,
            /** Product Manager */
            \ControlPanel\Service\ProductManager::class => \ControlPanel\Service\Factory\ProductManagerFactory::class,
            /** Store Manager */
            \ControlPanel\Service\StoreManager::class => \ControlPanel\Service\Factory\StoreManagerFactory::class,
            /** Auth Manager */
            \ControlPanel\Service\AuthManager::class => \ControlPanel\Service\Factory\AuthManagerFactory::class,
            /** Auth Adapter */
            \ControlPanel\Service\AuthAdapter::class => \ControlPanel\Service\Factory\AuthAdapterFactory::class,
            /** Authentication Service */
            //\Laminas\Authentication\AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
            'my_auth_service' => \ControlPanel\Service\Factory\AuthenticationServiceFactory::class,
            
        ],
    ],
    'parameters' => [
        '1c_provider_links' => [
            /** UserManager links */
            /** Provider login code */
            'lk_provider_login' => 'http://SRV02:8000/SC/hs/site/lk_provider_login',
            /** Create user */
            'lk_create_user' => 'http://SRV02:8000/SC/hs/site/lk_create_user',
            /** Update user */
            'lk_update_user' => 'http://SRV02:8000/SC/hs/site/lk_update_user',
            /** Get all users */
            'lk_get_all_users' => 'http://SRV02:8000/SC/hs/site/lk_get_all_users',
            /** ProductManager links */
            'lk_product_info' => 'http://SRV02:8000/SC/hs/site/lk_product_info',
            /** ProductManager; Edit product */
            'lk_edit_product' => ' http://SRV02:8000/SC/hs/site/lk_edit_product',
            /** StoreManager links */
            'lk_store_info' => 'http://SRV02:8000/SC/hs/site/lk_store_info',
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'control-panel' => __DIR__ . '/../view',
        ],
        'template_map' => [
//            'layout/control-panel'           => __DIR__ . '/../view/layout/control-panel.phtml',
//            'layout/control-panel-auth'     => __DIR__ . '/../view/layout/control-panel-auth.phtml',
            
            'control-panel/index/index' => __DIR__ . '/../view/control-panel/index/index.phtml',
//            'error/404'               => __DIR__ . '/../view/error/404.phtml',
//            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'strategies' => ['ViewJsonStrategy',],
    ],
    'controller_plugins' => [
        'factories' => [
            Controller\Plugin\AccessPlugin::class => Controller\Plugin\Factory\AccessPluginFactory::class,
            Controller\Plugin\CurrentUserPlugin::class => Controller\Plugin\Factory\CurrentUserPluginFactory::class,
        ],
        'aliases' => [
            'access' => Controller\Plugin\AccessPlugin::class,
            'currentUser' => Controller\Plugin\CurrentUserPlugin::class,
        ],
    ],    
    'view_helpers' => [
        'factories' => [
            View\Helper\Access::class => View\Helper\Factory\AccessFactory::class,
        ],
        'aliases' => [
            'access' => View\Helper\Access::class,
        ],
    ],
    
    
];