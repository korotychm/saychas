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
use Application\Controller\Factory\MyTestControllerFactory;
use Application\Controller\Factory\AjaxControllerFactory;
use Application\Controller\Factory\ReceivingControllerFactory;
use Laminas\Db\Adapter\AdapterAbstractServiceFactory;
//use Laminas\ServiceManager\Factory\InvokableFactory;
//use Application\Model\Factory\LaminasDbSqlRepositoryFactory;
use Application\Resource\StringResource;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;


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
                        'controller' => Controller\MyTestController::class,
                        'action'     => 'testing',
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
            'product' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/product[/:id]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'product',
                    ],
                ],
            ],
            'show-store' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/show-store',
                    'defaults' => [
                        'controller' => Controller\MyTestController::class,
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
            'receive-size' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/receive-size[/:id]',
                    'defaults' => [
                        'controller' => Controller\ReceivingController::class,
                        'action'     => 'receiveRepository',
                    ],
                    'repository' => \Application\Model\RepositoryInterface\SizeRepositoryInterface::class,
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
                    'repository' => \Application\Model\RepositoryInterface\CharacteristicValueRepositoryInterface::class,
                ],
            ],
            'hello-world' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/hello-world',
                    'defaults' => [
                        'controller' => Controller\MyTestController::class,
                        'action'     => 'helloWorld',
                    ],
                ],
            ],
            'test-repos' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/test-repos',
                    'defaults' => [
                        'controller' => Controller\MyTestController::class,
                        'action'     => 'testRepos',
                    ],
                ],
            ],
            'test-identity' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/test-identity',
                    'defaults' => [
                        'controller' => Controller\MyTestController::class,
                        'action'     => 'testIdentity',
                    ],
                ],
            ],
            'test-client' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/test-client',
                    'defaults' => [
                        'controller' => Controller\MyTestController::class,
                        'action'     => 'testClient',
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
                        'controller' => Controller\MyTestController::class,
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
            'banzaii' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/banzaii[/:id]',
                    'defaults' => [
                        'controller' => Controller\AjaxController::class,
                        'action'     => 'banzaii',
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
            'ajax-fltr' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/ajax-fltr',
                    'defaults' => [
                        'controller' => Controller\AjaxController::class,
                        'action'     => 'setFilterForCategory',
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
            Controller\MyTestController::class => MyTestControllerFactory::class,
            Controller\AjaxController::class => AjaxControllerFactory::class,
            Controller\ReceivingController::class => ReceivingControllerFactory::class,
        ],
    ],
    'laminas-cli' => [
        'commands' => [
            'package:fetch-images' => \Application\Command\FetchImagesCommand::class,
        ],
    ],
//    'service_manager' => [
//        'factories' => [
//            \Application\Command\FetchImagesCommand::class => \Application\Command\Factory\FetchImagesCommandFactory::class,
//        ],
//    ],

    'service_manager' => [
//        'abstract_factories' => [
//            ReflectionBasedAbstractFactory::class,
//        ],
        'aliases' => [
            //\Application\Model\TestRepositoryInterface::class => \Application\Model\TestRepository::class,
            \Application\Model\TestRepositoryInterface::class => \Application\Model\LaminasDbSqlRepository::class,
            \Application\Model\RepositoryInterface\CategoryRepositoryInterface::class => \Application\Model\Repository\CategoryRepository::class,
            \Application\Model\RepositoryInterface\ProviderRepositoryInterface::class => \Application\Model\Repository\ProviderRepository::class,
            \Application\Model\RepositoryInterface\StoreRepositoryInterface::class => \Application\Model\Repository\StoreRepository::class,
            \Application\Model\RepositoryInterface\ProviderRelatedStoreRepositoryInterface::class => \Application\Model\Repository\ProviderRelatedStoreRepository::class,
            \Application\Model\RepositoryInterface\ProductRepositoryInterface::class => \Application\Model\Repository\ProductRepository::class,
            \Application\Model\RepositoryInterface\FilteredProductRepositoryInterface::class => \Application\Model\Repository\FilteredProductRepository::class,
            \Application\Model\RepositoryInterface\PriceRepositoryInterface::class => \Application\Model\Repository\PriceRepository::class,
            \Application\Model\RepositoryInterface\StockBalanceRepositoryInterface::class => \Application\Model\Repository\StockBalanceRepository::class,
            \Application\Model\RepositoryInterface\SizeRepositoryInterface::class => \Application\Model\Repository\SizeRepository::class,
            \Application\Model\RepositoryInterface\BrandRepositoryInterface::class => \Application\Model\Repository\BrandRepository::class,
            \Application\Model\RepositoryInterface\CharacteristicRepositoryInterface::class => \Application\Model\Repository\CharacteristicRepository::class,
            \Application\Model\RepositoryInterface\CharacteristicValueRepositoryInterface::class => \Application\Model\Repository\CharacteristicValueRepository::class,
            \Application\Model\RepositoryInterface\CharacteristicValue2RepositoryInterface::class => \Application\Model\Repository\CharacteristicValue2Repository::class,
            \Application\Model\RepositoryInterface\ProductImageRepositoryInterface::class => \Application\Model\Repository\ProductImageRepository::class,
            \Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface::class => \Application\Model\Repository\HandbookRelatedProductRepository::class,

            \Laminas\Authentication\AuthenticationServiceInterface::class => \Laminas\Authentication\AuthenticationService::class,
            //\Application\Service\ServiceInterface\HtmlProviderServiceInterface::class => \Application\Service\HtmlProviderService::class,

            
            //'my_auth_service' => \Laminas\Authentication\AuthenticationService::class,
//            \Laminas\Authentication\AuthenticationService\AuthenticationService::class => 'my_auth_service',
        ],
        'factories' => [
            //\Laminas\View\HelperPluginManager => ReflectionBasedAbstractFactory::class,
            //'Application\Db\WriteAdapter' => AdapterAbstractServiceFactory::class,
            //\Application\Model\TestRepository::class => InvokableFactory::class,
            'Application\Db\WriteAdapter' => AdapterAbstractServiceFactory::class,
            \Laminas\Db\Adapter\AdapterInterface::class => \Laminas\Db\Adapter\Adapter::class,
            \Application\Model\LaminasDbSqlRepository::class => \Application\Model\Factory\LaminasDbSqlRepositoryFactory::class,
            \Application\Model\Repository\CategoryRepository::class => \Application\Model\Factory\CategoryRepositoryFactory::class,
            \Application\Model\Repository\ProviderRepository::class => \Application\Model\Factory\ProviderRepositoryFactory::class,
            \Application\Model\Repository\StoreRepository::class => \Application\Model\Factory\StoreRepositoryFactory::class,
            \Application\Model\Repository\ProviderRelatedStoreRepository::class => \Application\Model\Factory\ProviderRelatedStoreRepositoryFactory::class,
            \Application\Model\Repository\ProductRepository::class => \Application\Model\Factory\ProductRepositoryFactory::class,
            \Application\Model\Repository\FilteredProductRepository::class => \Application\Model\Factory\FilteredProductRepositoryFactory::class,
            \Application\Model\Repository\PriceRepository::class => \Application\Model\Factory\PriceRepositoryFactory::class,
            \Application\Model\Repository\StockBalanceRepository::class => \Application\Model\Factory\StockBalanceRepositoryFactory::class,
            \Application\Model\Repository\SizeRepository::class => \Application\Model\Factory\SizeRepositoryFactory::class,
            \Application\Model\Repository\BrandRepository::class => \Application\Model\Factory\BrandRepositoryFactory::class,
            \Application\Model\Repository\CharacteristicRepository::class => \Application\Model\Factory\CharacteristicRepositoryFactory::class,
            \Application\Model\Repository\CharacteristicValueRepository::class => \Application\Model\Factory\CharacteristicValueRepositoryFactory::class,
            \Application\Model\Repository\CharacteristicValue2Repository::class => \Application\Model\Factory\CharacteristicValue2RepositoryFactory::class,
            \Application\Model\Repository\ProductImageRepository::class => \Application\Model\Factory\ProductImageRepositoryFactory::class,
            \Application\Model\Repository\HandbookRelatedProductRepository::class => \Application\Model\Factory\HandbookRelatedProductRepositoryFactory::class,
            \Application\Model\Repository\UserRepository::class => \Application\Model\Factory\UserRepositoryFactory::class,
            \Application\Model\Repository\PostRepository::class => \Application\Model\Factory\PostRepositoryFactory::class,
            //\Application\Hydrator\UserHydrator::class => \Application\Hydrator\Factory\UserHydratorFactory::class,
            \Application\Service\HtmlProviderService::class => \Application\Service\Factory\HtmlProviderServiceFactory::class,
            \Application\Service\HtmlFormProviderService::class => \Application\Service\Factory\HtmlFormProviderServiceFactory::class,

            \Application\Command\FetchImagesCommand::class => \Application\Command\Factory\CommandFactory::class,
            
            \Laminas\Authentication\AuthenticationService::class => \Laminas\ServiceManager\Factory\InvokableFactory::class,
            \Application\Adapter\Auth\UserAuthAdapter::class => Adapter\Auth\Factory\UserAuthAdapterFactory::class,
            
      ],
        'invokables' => [
//            'my_auth_service' => \Laminas\Authentication\AuthenticationService\AuthenticationService::class,
            \Laminas\View\HelperPluginManager::class => ReflectionBasedAbstractFactory::class,
        ]

    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'layout/preview'          => __DIR__ . '/../view/layout/menu.phtml',
            'layout/mainpagenew'      => __DIR__ . '/../view/layout/mainpagenew.phtml',
            
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => ['ViewJsonStrategy',],
    ],
    'view_helpers' => [
      'invokables' => [
         'catalog' => \Application\View\Helper\CatalogHelper::class,
      ],
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
        'catalog_to_save_images' => __DIR__.'/../../../public/images/product',
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
