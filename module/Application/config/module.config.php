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
use Application\Controller\Factory\UserDataControllerFactory;
use Application\Controller\Factory\AjaxControllerFactory;
use Application\Controller\Factory\ReceivingControllerFactory;
use Application\Controller\Factory\FtpControllerFactory;
use Laminas\Db\Adapter\AdapterAbstractServiceFactory;
//use Laminas\ServiceManager\Factory\InvokableFactory;
//use Application\Model\Factory\LaminasDbSqlRepositoryFactory;
//use Application\Resource\StringResource;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\Session;
//use Laminas\Router\Http\Regex;
//use Laminas\Router\Http\Hostname;


use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

//$route = Regex::factory([
//    'regex' => '/blog/(?<id>[a-zA-Z0-9_-]+)(\.(?<format>(json|html|xml|rss)))?',
//    'defaults' => [
//        'controller' => 'Application\Controller\MyTestController',
//        'action'     => 'view',
//        'format'     => 'html',
//    ],
//    'spec' => '/blog/%id%.%format%',
//]);            

return [
    'router' => [
        'routes' => [
//            'reg' => [
//                'type' => Regex::class,
//                'options' => [
////                    'regex' => '/blog/(?<id>[a-zA-Z0-9_-]+)(\.(?<format>(json|html|xml|rss)))?',
//                    'regex' => '/blog(\/(?<id>[0-9_-]*))?(\/(?<product_id>[0-9]*))?',
//                    'defaults' => [
//                        'controller' => Controller\MyTestController::class,
//                        'action'     => 'blog',
//                        'default'     => '1',
//                    ],
//                    'spec' => '/blog[/%id%[/%product_id%]]',
//                ]
//            ],
//            'blog' => [
//                'type'    => Segment::class,
//                'options' => [
//                    'route'    => '/blog[/:id[/:product_id]]',
//                    'defaults' => [
//                        'controller' => Controller\MyTestController::class,
//                        'action'     => 'blog',
//                    ],
//                    'spec' => '/blog[/%id%[/%product_id%]]',
//                ],
//            ],
            
//            'lk.saychas-z.local' => [
//                'type' => Hostname::class,
//                'options' => [
//                    'route' => ':3rd.:2nd.:1st',
//                    'constraints' => [
//                        ':3rd' => 'lk',
//                        ':2nd' => 'saychas-z',
//                        ':1st' => 'local',
//                    ],
//                ],
//                'may_terminate' => true,
//                'child_routes' => [
//                    'index' => [
//                        'type' => Literal::class,
//                        'options' => [
//                            'route' => '/index',
//                            'defaults' => [
//                                'controller' => \ControlPanel\Controller\IndexController::class,
//                                'action' => 'index',
//                            ],
//                        ],
//                        'may_terminate' => true,
//                    ],
//                ],
//            ],
            'save-user-data' =>  [
                'type' => Literal::class,
                'options' => [
                    'route' => '/save-user-data',
                    'defaults' => [
                        'controller' => Controller\UserDataController::class,
                        'action' => 'save',
                    ],
                ],
            ],
//            'create-user-data' =>  [
//                'type' => Literal::class,
//                'options' => [
//                    'route' => '/create-user-data',
//                    'defaults' => [
//                        'controller' => Controller\UserDataController::class,
//                        'action' => 'create',
//                    ],
//                ],
//            ],
            'clear-user-data' =>  [
                'type' => Literal::class,
                'options' => [
                    'route' => '/clear-user-data',
                    'defaults' => [
                        'controller' => Controller\UserDataController::class,
                        'action' => 'clear',
                    ],
                ],
            ],
            'add-user-data' =>  [
                'type' => Literal::class,
                'options' => [
                    'route' => '/add-user-data',
                    'defaults' => [
                        'controller' => Controller\MyTestController::class,
                        'action' => 'addUserData',
                    ],
                ],
            ],
            'send-registration-sms' =>  [
                'type' => Literal::class,
                'options' => [
                    'route' => '/send-registration-sms',
                    'defaults' => [
                        'controller' => Controller\UserDataController::class,
                        'action' => 'sendRegistrationSms',
                    ],
                ],
            ],
            'send-feedback-code' =>  [
                'type' => Literal::class,
                'options' => [
                    'route' => '/send-feedback-code',
                    'defaults' => [
                        'controller' => Controller\UserDataController::class,
                        'action' => 'codeFeedback',
                    ],
                ],
            ],
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
            /**/
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
            'basket' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/basket',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'basket',
                    ],
                ],
            ],
            'basket-pay-info' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/ajax-basket-pay-info',
                    'defaults' => [
                        'controller' => Controller\AjaxController::class,
                        'action'     => 'basketPayInfo',
                    ],
                ],
            ],
            'basket-pay-card-info' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/ajax-basket-pay-card-info',
                    'defaults' => [
                        'controller' => Controller\AjaxController::class,
                        'action'     => 'basketPayCardInfo',
                    ],
                ],
            ],
            //basketPayInfo
            
        'user-auth-modal' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/user-auth-modal',
                    'defaults' => [
                        'controller' => Controller\UserDataController::class,
                        'action'     => 'userAuthModal',
                    ],
                ],
            ],//userAuthModal
            'send-basket-data' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/send-basket-data',
                    'defaults' => [
                        'controller' => Controller\UserDataController::class,
                        'action'     => 'sendBasketData',
                    ],
                ],
            ],//userAuth
            'product' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/product[/:id]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'product',
                    ],
                ],
            ],/**/
            
            
            /*'catalog' => [
                // First we define the basic options for the parent route: \Laminas\Router\Http\
                'type' => Segment::class,
                'options' => [
                    'route'    => '/catalog/:id',
                    'defaults' => [
                        'controller' => Controller\indexController::class,
                        'action'     => 'catalog',
                    ],
                    'constraints' => [
                        'id' => '(\d)+',
                    ],
                ],
                'may_terminate' => true, // \Laminas\Router\Http\
                'child_routes' => [
                    'product' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '[/:product_id]',
                            'defaults' => [
                                'controller' => Controller\indexController::class,
                                'action'     => 'product',
                            ],
//                            'constraints' => [
//                                'product_id' => '(\d)+',
//                            ],
                        ],
                    ],
                ],
            ],/**/
            
            
            
            
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
            'receive-color' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/receive-color[/:id]',
                    'defaults' => [
                        'controller' => Controller\ReceivingController::class,
                        'action'     => 'receiveRepository',
                    ],
                    'repository' => \Application\Model\RepositoryInterface\ColorRepositoryInterface::class,
                ],
            ],
            'receive-setting' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/receive-setting[/:id]',
                    'defaults' => [
                        'controller' => Controller\ReceivingController::class,
                        'action'     => 'receiveRepository',
                    ],
                    'repository' => \Application\Model\RepositoryInterface\SettingRepositoryInterface::class,
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
            'receive-marker' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/receive-marker[/:id]',
                    'defaults' => [
                        'controller' => Controller\ReceivingController::class,
                        'action'     => 'receiveRepository',
                    ],
                    'repository' => \Application\Model\RepositoryInterface\MarkerRepositoryInterface::class,
                ],
            ],
            'set-client-info' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/set-client-info[/:id]',
                    'defaults' => [
                        'controller' => Controller\UserDataController::class,
                        'action'     => 'setClientInfo',
                    ],
                ],
            ],
            'get-client-info' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/get-client-info[/:id]',
                    'defaults' => [
                        'controller' => Controller\UserDataController::class,
                        'action'     => 'getClientInfo',
                    ],
                ],
            ],
            'update-client-info' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/update-client-info[/:id]',
                    'defaults' => [
                        'controller' => Controller\UserDataController::class,
                        'action'     => 'updateClientInfo',
                    ],
                ],
            ],
            'change-client-password' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/change-client-password[/:id]',
                    'defaults' => [
                        'controller' => Controller\UserDataController::class,
                        'action'     => 'changeClientPassword',
                    ],
                ],
            ],
            'client-login' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/client-login[/:id]',
                    'defaults' => [
                        'controller' => Controller\UserDataController::class,
                        'action'     => 'clientLogin',
                    ],
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
            'get-image' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/get-image',
                    'defaults' => [
                        'controller' => Controller\FtpController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'get-image2' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/get-image2/:table/:fileName',
                    'defaults' => [
                        'controller' => Controller\FtpController::class,
                        'action'     => 'getImage',
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
            'blog' => [
                // First we define the basic options for the parent route: \Laminas\Router\Http\
                'type' => Segment::class,
                'options' => [
                    'route'    => '/blog/:id',
                    'defaults' => [
                        'controller' => Controller\MyTestController::class,
                        'action'     => 'blog',
                    ],
                    'constraints' => [
                        'id' => '(\d)+',
                    ],
                ],
                'may_terminate' => true, // \Laminas\Router\Http\
                'child_routes' => [
                    'product' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '[/product/:product_id]',
                            'defaults' => [
                                'controller' => Controller\MyTestController::class,
                                'action'     => 'product',
                            ],
//                            'constraints' => [
//                                'product_id' => '(\d)+',
//                            ],
                        ],
                    ],
                ],
            ],
            
            'cat' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/cat[/:id][/:product_id]',
                    'defaults' => [
                        'controller' => Controller\MyTestController::class,
                        'action' => 'cat',
                    ],
//                    'constraints' => [
//                        'product_id' => '(\d)+',
//                    ],
                ],
            ],
            
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
            'ajax-add-user-address'=>[
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/ajax-add-user-address',
                    'defaults' => [
                        'controller' => Controller\AjaxController::class,
                        'action'     => 'ajaxAddUserAddress',
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
//            'regex' => [
//                'regex' => '/blog/(?<id>[a-zA-Z0-9_-]+)(\.(?<format>(json|html|xml|rss)))?',
//                'defaults' => [
//                    'controller' => 'Application\Controller\BlogController',
//                    'action'     => 'view',
//                    'format'     => 'html',
//                ],
//                'spec' => '/blog/%id%.%format%',
//            ],

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
            'user' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/user',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'user',
                    ],
                ],
            ],
            
            'user-auth' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/ajax/user-auth',
                    'defaults' => [
                        'controller' => Controller\AjaxController::class,
                        'action'     => 'userAuth',
                    ],
                ],
            ],
            //basketOrderMergeAction
            'ajax-basket-order-merge'=> [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/ajax-basket-order-merge',
                    'defaults' => [
                        'controller' => Controller\AjaxController::class,
                        'action'     => 'basketOrderMerge',
                    ],
                ],
            ],
            'add-to-basket'=> [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/ajax/add-to-basket',
                    'defaults' => [
                        'controller' => Controller\AjaxController::class,
                        'action'     => 'addToBasket',
                    ],
                ],
            ],
             'del-from-basket'=> [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/ajax/del-from-basket',
                    'defaults' => [
                        'controller' => Controller\AjaxController::class,
                        'action'     => 'delFromBasket',
                    ],
                ],
            ],
            'calculate-basket-item'=> [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/ajax/calculate-basket-item',
                    'defaults' => [
                        'controller' => Controller\AjaxController::class,
                        'action'     => 'calculateBasketItem',
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
            Controller\UserDataController::class => UserDataControllerFactory::class,
            Controller\AjaxController::class => AjaxControllerFactory::class,
            Controller\ReceivingController::class => ReceivingControllerFactory::class,
            Controller\FtpController::class => FtpControllerFactory::class,
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
            \Application\Model\RepositoryInterface\MarkerRepositoryInterface::class => \Application\Model\Repository\MarkerRepository::class,
            \Application\Model\RepositoryInterface\BasketRepositoryInterface::class => \Application\Model\Repository\BasketRepository::class,
            \Application\Model\RepositoryInterface\ProductFavoritesRepositoryInterface::class => \Application\Model\Repository\ProductFavoritesRepository::class,
            \Application\Model\RepositoryInterface\ProductHistoryRepositoryInterface::class => \Application\Model\Repository\ProductHistoryRepository::class,
            \Application\Model\RepositoryInterface\ColorRepositoryInterface::class => \Application\Model\Repository\ColorRepository::class,
            \Application\Model\RepositoryInterface\ProductCharacteristicRepositoryInterface::class => \Application\Model\Repository\ProductCharacteristicRepository::class,
            \Application\Model\RepositoryInterface\SettingRepositoryInterface::class => \Application\Model\Repository\SettingRepository::class,
            \Application\Model\RepositoryInterface\CountryRepositoryInterface::class => \Application\Model\Repository\CountryRepository::class,
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
            \Application\Model\Repository\MarkerRepository::class => \Application\Model\Factory\MarkerRepositoryFactory::class,
            \Application\Model\Repository\BasketRepository::class => \Application\Model\Factory\BasketRepositoryFactory::class,
            \Application\Model\Repository\ProductFavoritesRepository::class => \Application\Model\Factory\ProductFavoritesRepositoryFactory::class,
            \Application\Model\Repository\ProductHistoryRepository::class => \Application\Model\Factory\ProductHistoryRepositoryFactory::class,
            \Application\Model\Repository\ColorRepository::class => \Application\Model\Factory\ColorRepositoryFactory::class,
            \Application\Model\Repository\ProductCharacteristicRepository::class => \Application\Model\Factory\ProductCharacteristicRepositoryFactory::class,
            \Application\Model\Repository\SettingRepository::class => \Application\Model\Factory\SettingRepositoryFactory::class,
            \Application\Model\Repository\CountryRepository::class => \Application\Model\Factory\CountryRepositoryFactory::class,
            \Application\Model\Repository\CharacteristicRepository::class => \Application\Model\Factory\CharacteristicRepositoryFactory::class,
            \Application\Model\Repository\CharacteristicValueRepository::class => \Application\Model\Factory\CharacteristicValueRepositoryFactory::class,
            \Application\Model\Repository\CharacteristicValue2Repository::class => \Application\Model\Factory\CharacteristicValue2RepositoryFactory::class,
            \Application\Model\Repository\ProductImageRepository::class => \Application\Model\Factory\ProductImageRepositoryFactory::class,
            \Application\Model\Repository\HandbookRelatedProductRepository::class => \Application\Model\Factory\HandbookRelatedProductRepositoryFactory::class,
            \Application\Model\Repository\UserRepository::class => \Application\Model\Factory\UserRepositoryFactory::class,
            \Application\Model\Repository\UserDataRepository::class => \Application\Model\Factory\UserDataRepositoryFactory::class,
            \Application\Model\Repository\PostRepository::class => \Application\Model\Factory\PostRepositoryFactory::class,
            //\Application\Hydrator\UserHydrator::class => \Application\Hydrator\Factory\UserHydratorFactory::class,
            \Application\Service\HtmlProviderService::class => \Application\Service\Factory\HtmlProviderServiceFactory::class,
            \Application\Service\HtmlFormProviderService::class => \Application\Service\Factory\HtmlFormProviderServiceFactory::class,
            \Application\Service\ExternalCommunicationService::class => \Application\Service\Factory\ExternalCommunicationServiceFactory::class,

            \Application\Command\FetchImagesCommand::class => \Application\Command\Factory\FetchImagesCommandFactory::class,
            
            \Laminas\Authentication\AuthenticationService::class => \Laminas\ServiceManager\Factory\InvokableFactory::class,
            \Application\Adapter\Auth\UserAuthAdapter::class => Adapter\Auth\Factory\UserAuthAdapterFactory::class,
            
            //'Laminas\Session\Config\ConfigInterface' => 'Laminas\Session\Service\SessionConfigFactory',
            //\Laminas\Session\Config\ConfigInterface::class => \Laminas\Session\Service\SessionConfigFactory::class,
            
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
        'not-authorized'           => 'error/403',
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
            'get_store' => 'http://SRV02:8000/SC/hs/site/get_store',
            'send_registration_code' => 'http://SRV02:8000/SC/hs/site/send_registration_code',
            'get_client_info' => 'http://SRV02:8000/SC/hs/site/get_client_info',
            'set_client_info' => 'http://SRV02:8000/SC/hs/site/set_client_info',
            'update_client_info' => 'http://SRV02:8000/SC/hs/site/update_client_info',
            'change_client_password' => 'http://SRV02:8000/SC/hs/site/change_client_password',
            'client_login' => 'http://SRV02:8000/SC/hs/site/client_login',
            'send_basket' => 'http://SRV02:8000/SC/hs/site/create_order',
        ],
        'catalog_to_save_images' => __DIR__.'/../../../public/images/product',
        'local_catalog' => [
            'product' => [
                'path' => __DIR__.'/../../../public/images/product',
            ],
            'brand' => [
                'path' => __DIR__.'/../../../public/images/brand',
            ],
            'provider' => [
                'path' => __DIR__.'/../../../public/images/provider',
            ],
        ],
        'server_catalog' => [
            'product' => [
                'path' => '/1CMEDIA/PhotoTovarov/',
            ],
            'brand' => [
                'path' => '/1CMEDIA/BrandLogo/',
            ],
            'provider' => [
                'path' => '/1CMEDIA/ProviderLogo/',
            ],
        ],
        'ftp_server' => [
            "domain" => "nas01.saychas.office",
            "username" => "1C",
            "password" => "ree7EC2A",
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
//    'session_manager' => [
//        'config' => [
//            'class' => \Session\Config\SessionConfig::class,
//            'options' => [
//                'name' => 'saychasapp',
//            ],
//        ],
//        'storage' => \Session\Storage\SessionArrayStorage::class,
//        'validators' => [
//            \Session\Validator\RemoteAddr::class,
//            \Session\Validator\HttpUserAgent::class,
//        ],
//    ],    
//    'session_containers' => [
//        StringResource::SESSION_NAMESPACE,
//    ],
//    'session_storage' => [
//        'type' => Laminas\Session\Storage\SessionArrayStorage::class,
//    ],
//    'session_config'  => [
//        'gc_maxlifetime' => 7200,
//        // …
//    ],
];
