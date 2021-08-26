<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use Laminas\Mvc\MvcEvent;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Response;
use Application\Model\TestRepositoryInterface;
use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
use Application\Model\RepositoryInterface\ProviderRepositoryInterface;
use Application\Model\RepositoryInterface\StoreRepositoryInterface;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;
use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;
use Application\Model\RepositoryInterface\BrandRepositoryInterface;
use Application\Model\RepositoryInterface\BasketRepositoryInterface;
use Application\Model\RepositoryInterface\ColorRepositoryInterface;
use Application\Model\RepositoryInterface\SettingRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Application\Model\Entity\ProductCharacteristic;
use Application\Model\RepositoryInterface\ProductCharacteristicRepositoryInterface;
use Application\Model\Repository\UserRepository;
use Application\Model\Repository\CharacteristicRepository;
use Application\Model\Entity\HandbookRelatedProduct;
use Application\Model\Entity\Provider;
use Application\Model\Entity\Setting;
use Application\Model\Entity\ClientOrder;
use Application\Model\Entity\Delivery;
use Application\Model\Entity\Basket;
use Laminas\Json\Json;
use Application\Service\HtmlProviderService;
use Application\Service\AcquiringCommunicationService;
use Application\Resource\Resource;
use Laminas\Session\Container; // as SessionContainer;
use Laminas\Session\SessionManager;
use Application\Adapter\Auth\UserAuthAdapter;
use Laminas\Db\Sql\Where;
use Application\Model\Entity\User;
use Application\Model\Entity\UserData;
use Application\Helper\StringHelper;
use Application\Model\Entity\ProductFavorites;
use Application\Model\Entity\ProductHistory;
use Application\Service\CommonHelperFunctionsService;

class AcquiringController extends AbstractActionController
{

    /**
     * @var TestRepositoryInterface
     */
    private $testRepository;
    private $categoryRepository;
    private $providerRepository;
    private $storeRepository;
    private $productRepository;
    private $filteredProductRepository;
    private $brandRepository;
    private $settingRepository;
    private $characteristicRepository;
    private $priceRepository;
    private $stockBalanceRepository;
    private $handBookRelatedProductRepository;
    private $entityManager;
    private $config;
    private $htmlProvider;
    private $acquiringCommunication;
    private $userRepository;
    private $authService;
    private $productCharacteristicRepository;
    private $colorRepository;
    private $basketRepository;
    //private $sessionContainer;
    private $sessionManager;
    private $commonHelperFuncions;

    public function __construct(TestRepositoryInterface $testRepository, CategoryRepositoryInterface $categoryRepository,
            ProviderRepositoryInterface $providerRepository, StoreRepositoryInterface $storeRepository,
            ProductRepositoryInterface $productRepository, FilteredProductRepositoryInterface $filteredProductRepository,
            BrandRepositoryInterface $brandRepository, ColorRepositoryInterface $colorRepository, SettingRepositoryInterface $settingRepository,
            CharacteristicRepositoryInterface $characteristicRepository,
            PriceRepositoryInterface $priceRepository, StockBalanceRepositoryInterface $stockBalanceRepository,
            HandbookRelatedProductRepositoryInterface $handBookProduct,
            $entityManager, $config, HtmlProviderService $htmlProvider, AcquiringCommunicationService $acquiringCommunication, UserRepository $userRepository, AuthenticationService $authService,
            ProductCharacteristicRepositoryInterface $productCharacteristicRepository, BasketRepositoryInterface $basketRepository/* , $sessionContainer */, $sessionManager,
            CommonHelperFunctionsService $commonHelperFuncions)
    {
        $this->testRepository = $testRepository;
        $this->categoryRepository = $categoryRepository;
        $this->providerRepository = $providerRepository;
        $this->storeRepository = $storeRepository;
        $this->productRepository = $productRepository;
        $this->filteredProductRepository = $filteredProductRepository;
        $this->brandRepository = $brandRepository;
        $this->colorRepository = $colorRepository;
        $this->settingRepository = $settingRepository;
        $this->characteristicRepository = $characteristicRepository;
        $this->priceRepository = $priceRepository;
        $this->stockBalanceRepository = $stockBalanceRepository;
        $this->handBookRelatedProductRepository = $handBookProduct;
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->htmlProvider = $htmlProvider;
        $this->acquiringCommunication = $acquiringCommunication;
        $this->userRepository = $userRepository;
        $this->authService = $authService;
        $this->productCharacteristicRepository = $productCharacteristicRepository;
        $this->basketRepository = $basketRepository;
//        $this->sessionContainer = $sessionContainer;
        $this->sessionManager = $sessionManager;
        $this->commonHelperFuncions = $commonHelperFuncions;

        $this->entityManager->initRepository(ClientOrder::class);
        $this->entityManager->initRepository(Setting::class);
        $this->entityManager->initRepository(Delivery::class);
    }

    public function onDispatch(MvcEvent $e)
    {
        $userAuthAdapter = new UserAuthAdapter(/* $this->userRepository *//* $this->sessionContainer */);
        $result = $this->authService->authenticate($userAuthAdapter);
        $code = $result->getCode();
        if ($code != \Application\Adapter\Auth\UserAuthResult::SUCCESS) {
            throw new \Exception('Unknown error in AcquiringController');
        }
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);
        return $response;
    }
    
    /*
     * @return JsonModel
     */
    public function tinkoffPaymentAction() 
    {
        //$param['apiconfig'] = $this->config['parameters']['TinkoffMerchantAPI'];
        
        $paramApi = $this->config['parameters']['TinkoffMerchantAPI'];
        
        $container = new Container(Resource::SESSION_NAMESPACE);
        $userId = $container->userIdentity;
        $orderId = $this->params()->fromRoute('order', '');
        
        $param = [
            'OrderId' => $orderId.time(),
           // "RedirectDueDate" => date(DATE_ISO8601, (time() + $paramApi['time_order_live'] )),
           // "SuccessURL"  =>  $paramApi['success_url'],     
           // "FailURL"=> $paramApi['fail_url'],     
            "Description"=> str_replace("<OrderId/>", $orderId, Resource::ORDER_PAYMENT_TITLE),     
          ];
        //$param['OrderId'] = ;
        $order = ClientOrder::find(["order_id" => $orderId]); 
        
        if (empty($order)) {
            return new JsonModel(["result" => false, "message" => "error: order ".$orderId." not found" ]);
        }
        $basket_info = Json::decode($order->getBasketInfo(), Json::TYPE_ARRAY); 
        $delivery_price = (int)$basket_info['delivery_price'];
        $delivery_params= Json::decode(Setting::find(["id" => "delivery_params"])->getValue(), Json::TYPE_ARRAY); 
        $delivery_tax = (int)$delivery_params['deliveryTax'];
        $userInfo = $this->htmlProvider->getUserInfo($this->userRepository->find(['id' => $userId]));
        if (empty($userInfo ['phone'])){
             return new JsonModel(["result" => false, "message" => "error: user phone not found" ]);
        }
        $param['DATA']['CustomerKey'] = $param['CustomerKey'] = $userInfo ['userid'];
        $param['DATA']['Phone'] =  $param['Receipt']['Phone'] =  "+".$userInfo['phone'];
        if($userInfo['email']){
            $param['DATA']['Email'] =  $param['Receipt']['Email'] =  $userInfo['email'];
        }    
        $param['Receipt']['EmailCompany'] = $paramApi['company_email'];
        $param['Receipt']['Taxation'] = $paramApi['company_taxation'];
            
        $basket = Basket::findAll(["where" => ['user_id' => $userId, 'order_id' => $orderId]]);
        
        if (empty($basket)) {
            return new JsonModel(["result" => false, "message" => "error: products of order not found " ]);
        }
        
        $orderItems = $this->acquiringCommunication->getBasketData($basket);
        $param['Receipt']['Items'] =  $orderItems['Items'];
        $param['Amount'] = $orderItems['Amount'];
        $vat=($delivery_tax < 0)?"none":"vat".$delivery_tax;
        if ($delivery_price > 0) {
            $param['Receipt']['Items'][] = [
               'Name' => Resource::ORDER_PAYMENT_DELIVERY,
               'Quantity' => 1,
               'PaymentObject' => "service",
               'Amount' => $delivery_price,
               'Price' => $delivery_price,
               'Tax' => $vat,
            ];
            $param['Amount']+=$delivery_price;
        }
        //return new JsonModel($param);
            //$message = print_r($param, true);
            //mail("plusweb@localhost", "tinkoff.log", $message);
        
        $tinkoffAnswer = $this->acquiringCommunication->initTinkoff($param);
        if ($tinkoffAnswer['answer']["ErrorCode"] === "0") {
            return new JsonModel(["result" => true, "answer" =>$tinkoffAnswer['answer']]);
        }
        return new JsonModel(["result" => false, "answer" => $this->acquiringCommunication->initTinkoff($param)]);
    }
    
    public function tinkoffErrorAction()
    {
        $param=["type"=>"error"];
        return new JsonModel( $param);
    }
    
    public function tinkoffSuccessAction()
    {
        $param=["type"=>"Success"];
        return new JsonModel( $param);
    }
    public function tinkoffCallbackAction()
    {
            $post = $this->getRequest(); //->getPost()->toArray();
            $message = print_r($_POST, true);
            mail("user@localhost", "tinkoff.log", $message);
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_200);
            $response->setContent('OK');
            return $response;
    }
  
}
