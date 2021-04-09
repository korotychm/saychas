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
use Application\Controller\Factory\AjaxControllerFactory;
use Application\Controller\Factory\ReceivingControllerFactory;
use Laminas\Db\Adapter\AdapterAbstractServiceFactory;
//use Laminas\ServiceManager\Factory\InvokableFactory;
//use Application\Model\Factory\LaminasDbSqlRepositoryFactory;
use Application\Resource\StringResource;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

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
//            'show-provider' => [
//                'type'    => Segment::class,
//                'options' => [
//                    'route'    => '/show-provider[/:id]',
//                    'defaults' => [
//                        'controller' => Controller\ReceivingController::class,
//                        'action'     => 'showProvider',
//                    ],
//                ],
//            ],
            'testing' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/testing[/:id]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'testing',
                    ],
                ],
            ],
            'show-product' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/show-product[/:id]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'showProduct',
                    ],
                ],
            ],
            'provider' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/provider',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'provider',
                    ],
                ],
            ],
            'catalog' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/catalog[/:id]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'catalog',
                    ],
                ],
            ],
            'show-store' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/show-store',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'showStore',
                    ],
                ],
            ],
            'receive-provider' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/receive-provider[/:id]',
                    'defaults' => [
                        'controller' => Controller\ReceivingController::class,
                        'action'     => 'receiveRepository',
                    ],
                    'repository' => \Application\Model\RepositoryInterface\ProviderRepositoryInterface::class,
                ],
            ],
            'receive-store' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/receive-store[/:id]',
                    'defaults' => [
                        'controller' => Controller\ReceivingController::class,
                        'action'     => 'receiveRepository',
                    ],
                    'repository' => \Application\Model\RepositoryInterface\StoreRepositoryInterface::class,
                ],
            ],
            'receive-product' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/receive-product[/:id]',
                    'defaults' => [
                        'controller' => Controller\ReceivingController::class,
                        'action'     => 'receiveRepository',
                    ],
                    'repository' => \Application\Model\RepositoryInterface\ProductRepositoryInterface::class,
                ],
            ],
            'receive-price' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/receive-price[/:id]',
                    'defaults' => [
                        'controller' => Controller\ReceivingController::class,
                        'action'     => 'receiveRepository',
                    ],
                    'repository' => \Application\Model\RepositoryInterface\PriceRepositoryInterface::class,
                ],
            ],
            'receive-stock-balance' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/receive-stock-balance[/:id]',
                    'defaults' => [
                        'controller' => Controller\ReceivingController::class,
                        'action'     => 'receiveRepository',
                    ],
                    'repository' => \Application\Model\RepositoryInterface\StockBalanceRepositoryInterface::class,
                ],
            ],
            'receive-category' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/receive-category[/:id]',
                    'defaults' => [
                        'controller' => Controller\ReceivingController::class,
                        'action'     => 'receiveRepository',
                    ],
                    'repository' => \Application\Model\RepositoryInterface\CategoryRepositoryInterface::class,
                ],
            ],
            'receive-brand' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/receive-brand[/:id]',
                    'defaults' => [
                        'controller' => Controller\ReceivingController::class,
                        'action'     => 'receiveRepository',
                    ],
                    'repository' => \Application\Model\RepositoryInterface\BrandRepositoryInterface::class,
                ],
            ],
            'receive-characteristic' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/receive-characteristic[/:id]',
                    'defaults' => [
                        'controller' => Controller\ReceivingController::class,
                        //'action'     => 'receiveCharacteristic',
                        'action'     => 'receiveRepository',
                    ],
                    'repository' => \Application\Model\RepositoryInterface\CharacteristicRepositoryInterface::class,
                ],
            ],
            'receive-predef-char-value' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/receive-predef-char-value[/:id]',
                    'defaults' => [
                        'controller' => Controller\ReceivingController::class,
                        'action'     => 'receiveRepository',
                    ],
                    'repository' => \Application\Model\RepositoryInterface\PredefCharValueRepositoryInterface::class,
                ],
            ],
            'hello-world' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/hello-world',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'helloWorld',
                    ],
                ],
            ],
            'test-repos' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/test-repos',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'testRepos',
                    ],
                ],
            ],
            
//            'receive' => [
//                // First we define the basic options for the parent route: \Laminas\Router\Http\
//                'type' => Literal::class,
//                'options' => [
//                    'route'    => '/receive',
//                    'defaults' => [
//                        'controller' => Controller\ReceivingController::class,
//                        'action'     => 'receive',
//                    ],
//                ],
//                'may_terminate' => true, // \Laminas\Router\Http\
//                'child_routes' => [
//                    'stock-balance' => [
//                        'type' => Segment::class,
//                        'options' => [
//                            'route'    => '/stock-balance',
//                            'defaults' => [
//                                'action' => 'receiveStockBalance',
//                            ],
//                            'constraints' => [
//                                
//                            ],
//                        ],
//                    ],
//                    'price' => [
//                        'type'    => Segment::class,
//                        'options' => [
//                            'route'    => '/price[/:id]',
//                            'defaults' => [
//                                'controller' => Controller\ReceivingController::class,
//                                'action'     => 'receivePrice',
//                            ],
//                        ],
//                    ],
//                ],
//            ],
            'add-new-post' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/add-new-post[/:id]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'addNewPost',
                    ],
                ],
            ],
            'ajax' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/ajax[/:id]',
                    'defaults' => [
                        'controller' => Controller\AjaxController::class,
                        'action'     => 'ajax',
                    ],
                ],
            ],
            'ajax-to-web' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/ajax-to-web',
                    'defaults' => [
                        'controller' => Controller\AjaxController::class,
                        'action'     => 'ajaxToWeb',
                    ],
                ],
            ],
           'ajax-get-store' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/ajax-get-store',
                    'defaults' => [
                        'controller' => Controller\AjaxController::class,
                        'action'     => 'ajaxGetStore',
                    ],
                ],
            ],
            'ajax-set-user-address'=>[
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/ajax-set-user-address',
                    'defaults' => [
                        'controller' => Controller\AjaxController::class,
                        'action'     => 'ajaxSetUserAddress',
                    ],
                ],
            ],            
            'ajax-get-legal-store'=> [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/ajax-get-legal-store',
                    'defaults' => [
                        'controller' => Controller\AjaxController::class,
                        'action'     => 'ajaxGetLegalStore',
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
            Controller\AjaxController::class => AjaxControllerFactory::class,
            Controller\ReceivingController::class => ReceivingControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'aliases' => [
            //\Application\Model\TestRepositoryInterface::class => \Application\Model\TestRepository::class,
            \Application\Model\TestRepositoryInterface::class => \Application\Model\LaminasDbSqlRepository::class,
            \Application\Model\RepositoryInterface\CategoryRepositoryInterface::class => \Application\Model\Repository\CategoryRepository::class,
            \Application\Model\RepositoryInterface\ProviderRepositoryInterface::class => \Application\Model\Repository\ProviderRepository::class,
            \Application\Model\RepositoryInterface\StoreRepositoryInterface::class => \Application\Model\Repository\StoreRepository::class,
            \Application\Model\RepositoryInterface\ProductRepositoryInterface::class => \Application\Model\Repository\ProductRepository::class,
            \Application\Model\RepositoryInterface\FilteredProductRepositoryInterface::class => \Application\Model\Repository\FilteredProductRepository::class,
            \Application\Model\RepositoryInterface\PriceRepositoryInterface::class => \Application\Model\Repository\PriceRepository::class,
            \Application\Model\RepositoryInterface\StockBalanceRepositoryInterface::class => \Application\Model\Repository\StockBalanceRepository::class,
            \Application\Model\RepositoryInterface\BrandRepositoryInterface::class => \Application\Model\Repository\BrandRepository::class,
            \Application\Model\RepositoryInterface\CharacteristicRepositoryInterface::class => \Application\Model\Repository\CharacteristicRepository::class,
            \Application\Model\RepositoryInterface\PredefCharValueRepositoryInterface::class => \Application\Model\Repository\PredefCharValueRepository::class,
            
            //\Application\Service\ServiceInterface\HtmlProviderServiceInterface::class => \Application\Service\HtmlProviderService::class,
        ],
        'factories' => [
            //'Application\Db\WriteAdapter' => AdapterAbstractServiceFactory::class,
            //\Application\Model\TestRepository::class => InvokableFactory::class,
            'Application\Db\WriteAdapter' => AdapterAbstractServiceFactory::class,
            \Laminas\Db\Adapter\AdapterInterface::class => \Laminas\Db\Adapter\Adapter::class,
            \Application\Model\LaminasDbSqlRepository::class => \Application\Model\Factory\LaminasDbSqlRepositoryFactory::class,
            \Application\Model\Repository\CategoryRepository::class => \Application\Model\Factory\CategoryRepositoryFactory::class,
            \Application\Model\Repository\ProviderRepository::class => \Application\Model\Factory\ProviderRepositoryFactory::class,
            \Application\Model\Repository\StoreRepository::class => \Application\Model\Factory\StoreRepositoryFactory::class,
            \Application\Model\Repository\ProductRepository::class => \Application\Model\Factory\ProductRepositoryFactory::class,
            \Application\Model\Repository\FilteredProductRepository::class => \Application\Model\Factory\FilteredProductRepositoryFactory::class,
            \Application\Model\Repository\PriceRepository::class => \Application\Model\Factory\PriceRepositoryFactory::class,
            \Application\Model\Repository\StockBalanceRepository::class => \Application\Model\Factory\StockBalanceRepositoryFactory::class,
            \Application\Model\Repository\BrandRepository::class => \Application\Model\Factory\BrandRepositoryFactory::class,
            \Application\Model\Repository\CharacteristicRepository::class => \Application\Model\Factory\CharacteristicRepositoryFactory::class,
            \Application\Model\Repository\PredefCharValueRepository::class => \Application\Model\Factory\PredefCharValueRepositoryFactory::class,
            \Application\Service\HtmlProviderService::class => \Application\Service\Factory\HtmlProviderServiceFactory::class,
            \Application\Service\HtmlFormProviderService::class => \Application\Service\Factory\HtmlFormProviderServiceFactory::class,
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
        'strategies' => ['ViewJsonStrategy',],
    ],
    'parameters' => [
        '1c_auth' => [
            'username' => 'administrator',
            'password' => 'w48Es4562',
        ],
        '1c_request_links' => [
            'get_product' => 'http://SRV02:8000/SC/hs/site/get_product',
            'get_store' => 'http://SRV02:8000/SC/hs/site/get_product',
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
//    'session_containers' => [
//        StringResource::SESSION_NAMESPACE,
//    ],    
];
