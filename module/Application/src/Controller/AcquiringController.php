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
//use Application\Model\RepositoryInterface\BrandRepositoryInterface;
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
use Application\Model\Entity\UserPaycard;
use Application\Model\Entity\Basket;
use Laminas\Json\Json;
//use Application\Service\HtmlProviderService;
use Application\Service\ExternalCommunicationService;
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
    //private $brandRepository;
    private $settingRepository;
    private $characteristicRepository;
    private $priceRepository;
    private $stockBalanceRepository;
    private $handBookRelatedProductRepository;
    private $entityManager;
    private $config;
    //private $htmlProvider;
    private $externalCommunication;
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
            /*BrandRepositoryInterface $brandRepository,*/ ColorRepositoryInterface $colorRepository, SettingRepositoryInterface $settingRepository,
            CharacteristicRepositoryInterface $characteristicRepository,
            PriceRepositoryInterface $priceRepository, StockBalanceRepositoryInterface $stockBalanceRepository,
            HandbookRelatedProductRepositoryInterface $handBookProduct,
            $entityManager, $config, /*HtmlProviderService $htmlProvider,*/ ExternalCommunicationService $externalCommunication, AcquiringCommunicationService $acquiringCommunication, UserRepository $userRepository, AuthenticationService $authService,
            ProductCharacteristicRepositoryInterface $productCharacteristicRepository, BasketRepositoryInterface $basketRepository/* , $sessionContainer */, $sessionManager,
            CommonHelperFunctionsService $commonHelperFuncions)
    {
        $this->testRepository = $testRepository;
        $this->categoryRepository = $categoryRepository;
        $this->providerRepository = $providerRepository;
        $this->storeRepository = $storeRepository;
        $this->productRepository = $productRepository;
        $this->filteredProductRepository = $filteredProductRepository;
        //$this->brandRepository = $brandRepository;
        $this->colorRepository = $colorRepository;
        $this->settingRepository = $settingRepository;
        $this->characteristicRepository = $characteristicRepository;
        $this->priceRepository = $priceRepository;
        $this->stockBalanceRepository = $stockBalanceRepository;
        $this->handBookRelatedProductRepository = $handBookProduct;
        $this->entityManager = $entityManager;
        $this->config = $config;
        //$this->htmlProvider = $htmlProvider;
        $this->externalCommunication = $externalCommunication;
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
        $this->entityManager->initRepository(UserPaycard::class);
        //$this->entityManager->initRepository(Basket::class);
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
        
        
        $container = new Container(Resource::SESSION_NAMESPACE);
        $userId = $container->userIdentity;
        $orderId = $this->params()->fromRoute('order', '');
        $userInfo = $this->commonHelperFuncions->getUserInfo($this->userRepository->find(['id' => $userId]));
        if (empty($userInfo ['phone'])){
             return new JsonModel(["result" => false, "message" => "error: user phone not found" ]);
        }
        $order = ClientOrder::find(["order_id" => $orderId,  "status" => 1]); 
        if (empty($order)) {
            return new JsonModel(["result" => false, "message" => "error: order ".$orderId." can't be paid" ]);
        }
        $basket_info = Json::decode($order->getBasketInfo(), Json::TYPE_ARRAY); 
        $delivery_price = (int)$basket_info['delivery_price'];
        $userInfo['paycard'] = $basket_info['paycard'];
                $param =$this->buildTinkoffArgs($orderId, $userInfo);
        
        $orderBasket = Basket::findAll(["where" => ['user_id' => $userId, 'order_id' => $orderId]]);
        
        if (empty($orderBasket)) {
            return new JsonModel(["result" => false, "message" => "error: products of order not found ",["where" => ['user_id' => $userId, 'order_id' => $orderId]] ]);
        }
        
        $orderItems = $this->acquiringCommunication->getOrderItems($orderBasket);
        $param['Receipt']['Items'] =  $orderItems['Items'];
        $param['Amount'] = $orderItems['Amount'];
        $param['RedirectDueDate'] = date('Y-m-d\TH:i:s+03:00', (time() + $this->config['parameters']['TinkoffMerchantAPI']['time_order_live'] ));
    
        //$vat=($delivery_tax < 0)?"none":"vat".$delivery_tax;
        if ($delivery_price > 0) {
            $param['Receipt']['Items'][] = $this->addDeliveryItem($delivery_price);
            $param['Amount']+=$delivery_price;
         }
        $tinkoffAnswer = $this->acquiringCommunication->initTinkoff($param);
        if ($tinkoffAnswer['answer']["ErrorCode"] === "0") {
            $order->setPaymentInfo($tinkoffAnswer['answer']);
            $order->persist(["order_id" => $orderId, "status" => 1 ]);
            return new JsonModel(["result" => true, 'param' => $param,  "answer" =>$tinkoffAnswer['answer']]);
        }
        return new JsonModel(["result" => false, 'param' => $param, "answer" => $tinkoffAnswer]);
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
    
    
    public function tinkoffOrderBillAction()
    {
        //$post[] = $this->getRequest()->getPost()->toArray(); 
        $Amount = 0;
        $json = file_get_contents('php://input');
        $post["post1C"] = Json::decode($json , Json::TYPE_ARRAY);  
        
        $orderId = $post["post1C"]["order_id"];
        $order = ClientOrder::find(['order_id' => $orderId]);
        $userId = $order->getUserId();
        //$user = ;
        //$post["user"] =
        $userInfo = $this->commonHelperFuncions->getUserInfo(User::find(["id" => $userId]));        
        $post["requestTinkoff"] = $this->buildTinkoffArgs($orderId, $userInfo);
        
         unset ( $post["requestTinkoff"]["OrderId"],
                 $post["requestTinkoff"]["Description"], 
                 $post["requestTinkoff"]["CustomerKey"]
                 );
        
        foreach ($post["post1C"]["products"] as $item){
            $item["Tax"] = ($item["Tax"] == null ) ? "none":"vat".$item["Tax"];
            $Amount +=  $item["Amount"] = $item["Price"] * $item["Quantity"];
            
            $post["requestTinkoff"]["Receipt"]["Items"][] = $item;
                
        }        
        $post["requestTinkoff"]["Receipt"]["Items"][] = $this->addDeliveryItem($post["post1C"]["amount_delevery"]);
        $Amount += $post["post1C"]["amount_delevery"];
        $post["requestTinkoff"]["Amount"]     = $Amount;
        $post["requestTinkoff"]["PaymentId"]  = $post["post1C"]["payment_id"];
        $post["requestTinkoff"]['SuccessURL'] = "https://saychas.ru/user/order/".$orderId;
        $post["requestTinkoff"]['FailURL']    = "https://saychas.ru/user/order/".$orderId;
        $order->setConfirmInfo($json);
        $order->persist(['order_id' => $orderId]);
        
        $post["answerTinkoff"] = $this->acquiringCommunication->confirmTinkoff($post["requestTinkoff"]);
        mail("d.sizov@saychas.ru", "confirm_payment_$orderId.log", print_r($post, true)); // лог на почту
        if (!empty($post["answerTinkoff"]['error'])) {
            return new JsonModel(['result' => false, 'description' => $post["answerTinkoff"]['error']]);
        }
        
        $response = $this->getResponse();
        $response->setStatusCode(Response::STATUS_CODE_200);
        //$post["answerTinkoff"][] 
        $answer = ['result' => true, 'description' => 'ok'];
        return new JsonModel($answer);
        
    }
    
    
    private function buildTinkoffArgs($orderId, $userInfo)
    {
        $paramApi = $this->config['parameters']['TinkoffMerchantAPI'];
        $param = [
            'OrderId' => $orderId,
            "Description"=> str_replace("<OrderId/>", $orderId, Resource::ORDER_PAYMENT_DESCRIPTION),     
            "CustomerKey" => $userInfo ['userid'],
            'DATA' => [
                "CustomerKey" => $userInfo ['userid'],
                'Phone' => "+".$userInfo['phone'],
                'DefaultCard' => $userInfo['paycard'],
               // 'Email' =>  $userInfo['email'],
                ],
            'Receipt' => [
                'Phone' => "+".$userInfo['phone'],
                'EmailCompany' => $paramApi['company_email'],
                'Taxation' => $paramApi['company_taxation'],
              ]
          ];
        if($userInfo['email']){
            $param['DATA']['Email'] =  $param['Receipt']['Email'] =  $userInfo['email'];
        }
        return $param;
    }
    
    
    private function addDeliveryItem ($delivery_price)
    {
        //$delivery_tax = $this->config['parameters']['TinkoffMerchantAPI']['deliveryTax'];
        //$vat = ($delivery_tax < 0)?"none":"vat".$delivery_tax;
        $vat = "vat20";
       // exit (print_r($this->config['parameters']['TinkoffMerchantAPI']));
        return [
            'Name' => Resource::ORDER_PAYMENT_DELIVERY,
            'Quantity' => 1,
            'PaymentObject' => "service",
            'Amount' => $delivery_price,
            'Price' => $delivery_price,
            'Tax' => $vat,
         ];
        
    }
    
    /*
     * get post json
     * @return response
     */
     
    public function tinkoffCallbackAction()
    {
        $jsonData = file_get_contents('php://input');    
        //mail("d.sizov@saychas.ru", "tinkoff.log", print_r($jsonData, true)); // лог на почту
        $postData = (!empty($jsonData))?Json::decode($jsonData, Json::TYPE_ARRAY):[];
        if ($postData["ErrorCode"] == "0"){
            $order = ClientOrder::find(["order_id" => $postData["OrderId"]]); 
            if (!empty($order)){
                    $order->setPaymentInfo($jsonData);
                    $order->persist(["order_id" => $postData["OrderId"]]);
            }        
       if (!empty($postData["CardId"]) and !empty($postData["OrderId"]) and  !empty($clientOrder = ClientOrder::find(["order_id" => $postData["OrderId"]]))){
                   
                       $postData['user']=$userId = $clientOrder->getUserId();
                       if (!empty($postData["CardId"] and !empty($postData["Pan"]))) {
                            $userPaycard = UserPaycard::findFirstOrDefault(['card_id' => $postData["CardId"], "user_id" => $userId]);
                            $userPaycard->setUserId($userId)->setCardId($postData["CardId"])->setPan($postData["Pan"])->setTime(time());
                            $userPaycard->persist(['card_id' => $postData["CardId"], "user_id" => $userId]);    
                       }
            } 
           $postData['answer_1с']=$this->externalCommunication->sendOrderPaymentInfo($postData);
        }
        mail("d.sizov@saychas.ru", "tinkoff.log", print_r($postData, true)); // лог на почту
        $response = new Response();
        $response->setStatusCode(Response::STATUS_CODE_200)->setContent('OK');
        return $response;
    }
  
}
