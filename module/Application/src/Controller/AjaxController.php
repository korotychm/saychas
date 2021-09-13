<?php
/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use Application\Model\TestRepositoryInterface;
use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
use Application\Model\RepositoryInterface\ProviderRepositoryInterface;
use Application\Model\RepositoryInterface\StoreRepositoryInterface;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;
use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;
use Application\Model\RepositoryInterface\BrandRepositoryInterface;
use Application\Model\RepositoryInterface\BasketRepositoryInterface;
use Application\Model\Entity\Basket;
use Application\Model\Entity\ClientOrder;
use Application\Model\Entity\Setting;
use Application\Model\Entity\Delivery;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\Repository\CharacteristicRepository;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Application\Model\Entity\HandbookRelatedProduct;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Application\Model\RepositoryInterface\ProductCharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\ProductImageRepositoryInterface;
use Application\Service\HtmlProviderService;
use Application\Service\CommonHelperFunctionsService;
use Application\Model\Entity\User;
use Application\Model\Entity\UserData;
use Application\Model\Entity\UserPaycard;
use Application\Model\Entity\ProductFavorites;
use Application\Model\Entity\ProductHistory;
use Application\Model\Entity\ProductCharacteristic;
//use Application\Model\Entity\Provider;
use Application\Model\Repository\UserRepository;
//use Application\Adapter\Auth\UserAuthAdapter;
//use RuntimeException;
use Laminas\Authentication\AuthenticationService;
use Application\Resource\Resource;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
//use Laminas\Http\Response;
use Laminas\Session\Container; // as SessionContainer;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Laminas\Db\Sql\Where;
//use Throwable;
//use Application\Helper\ArrayHelper;
use Application\Helper\StringHelper;

class AjaxController extends AbstractActionController
{

    private $testRepository;
    private $categoryRepository;
    private $providerRepository;
    private $storeRepository;
    private $productRepository;
    private $filteredProductRepository;
    private $brandRepository;
    private $characteristicRepository;
    private $priceRepository;
    private $stockBalanceRepository;
    private $handBookRelatedProductRepository;
    private $productCharacteristicRepository;
    private $entityManager;
    private $config;
    private $htmlProvider;
    private $userRepository;
    private $authService;
    private $basketRepository;
    private $productImageRepository;
    private $commonHelperFuncions;

    //private $sessionContainer;

    public function __construct(TestRepositoryInterface $testRepository, CategoryRepositoryInterface $categoryRepository,
            ProviderRepositoryInterface $providerRepository, StoreRepositoryInterface $storeRepository,
            ProductRepositoryInterface $productRepository, FilteredProductRepositoryInterface $filteredProductRepository, BrandRepositoryInterface $brandRepository,
            CharacteristicRepositoryInterface $characteristicRepository, PriceRepositoryInterface $priceRepository, StockBalanceRepositoryInterface $stockBalanceRepository,
            HandbookRelatedProductRepositoryInterface $handBookProduct, $entityManager, $config,
            HtmlProviderService $htmlProvider, UserRepository $userRepository, AuthenticationService $authService,
            ProductCharacteristicRepositoryInterface $productCharacteristicRepository, BasketRepositoryInterface $basketRepository, ProductImageRepositoryInterface $productImageRepository/* ,
              SessionContainer $sessionContainer */, CommonHelperFunctionsService $commonHelperFuncions)
    {
        $this->testRepository = $testRepository;
        $this->categoryRepository = $categoryRepository;
        $this->providerRepository = $providerRepository;
        $this->storeRepository = $storeRepository;
        $this->productRepository = $productRepository;
        $this->filteredProductRepository = $filteredProductRepository;
        $this->brandRepository = $brandRepository;
        $this->characteristicRepository = $characteristicRepository;
        $this->priceRepository = $priceRepository;
        $this->stockBalanceRepository = $stockBalanceRepository;
        $this->handBookRelatedProductRepository = $handBookProduct;
        $this->productCharacteristicRepository = $productCharacteristicRepository;
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->htmlProvider = $htmlProvider;
        $this->userRepository = $userRepository;
        $this->authService = $authService;
        $this->basketRepository = $basketRepository;
        $this->productImageRepository = $productImageRepository;
        $this->commonHelperFuncions = $commonHelperFuncions;
//        $this->sessionContainer = $sessionContainer;
        $this->entityManager->initRepository(ClientOrder::class);
        $this->entityManager->initRepository(Setting::class);
        $this->entityManager->initRepository(Delivery::class);
        $this->entityManager->initRepository(UserPaycard::class);
        $this->entityManager->initRepository(ProductFavorites::class);
        $this->entityManager->initRepository(ProductHistory::class);
    }

    
    public function ajaxGetBasketJsonAction()
    {
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
          if (!$return['userId']) {
            $this->getResponse()->setStatusCode(403);
            return;
        }
        $return['basket'] = [];
        $basket = Basket::findAll(['user_id' => $userId, 'order_id' => "0"]);
        if (null === $basket) {
            return new JsonModel($return);
        }
        foreach ($basket as $basketItem){
            $return['basket'][$basketItem->getProductId()] = [
                "id" => $basketItem->getProductId(),
                "total" => $basketItem->getTotal(),
                "price" => $basketItem->getPrice(),
                "discount" => $basketItem->getDiscount(),
            ];
        }
        return new JsonModel($return);
    }
    
    
    public function delFromBasketAction()
    {
        $return = ["error" => true, "count" => 0];
        $post = $this->getRequest()->getPost();
        $return['productId'] = $productId = $post->productId;
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        $basketItem = Basket::remove(['where' => ['user_id' => $userId, 'product_id' => $productId]]);
        return new JsonModel($return);
    }

    public function getJsonCategoryFiltersAction()
    {
        $post = $this->getRequest()->getPost();
        $return['category_id'] = $category_id = $post->categoryId;
        if (!$category_id or empty($matherCategories = $this->categoryRepository->findAllMatherCategories($category_id))) {
            return new JsonModel($return);
        }
        $categoryTree = $this->categoryRepository->findCategoryTree($category_id, [$category_id]);
        $return["rangeprice"] = $this->handBookRelatedProductRepository->findMinMaxPriceValueByCategory($categoryTree);
        $filters = $this->productCharacteristicRepository->getCategoryFilter($matherCategories);
        $return["filters"] = $this->htmlProvider->getCategoryFilterJson($filters, $category_id);
        return new JsonModel($return);
    }

    public function getUserOrderListAction()
    {
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        $return["result"] = true;
        $orders = ClientOrder::findAll(["where" => ['user_id' => $userId]]);
        if ($orders->count() < 1) {
            return new JsonModel(["result" => false]);
        }
        $return["order_list"] = $this->htmlProvider->orderList($orders);
        $productMap = $this->getBasketProductMap($userId);
        if ($productMap['result'] == false ) {
            return new JsonModel($productMap);
        }    
        $return["productsMap"] = $productMap['products'];
         
//        $where = new Where();
//        $where->equalTo('user_id', $userId);
//        $where->notEqualTo('order_id', 0);
//        $columns = ['product_id'];
//        $userBasketHistory = Basket::findAll(['where' => $where, 'columns' => $columns]);
//        if ($userBasketHistory->count() < 1) {
//            return new JsonModel(["result" => false]);
//        }
//        foreach ($userBasketHistory as $basketItem) {
//            $product_id = $basketItem->getProductId();
//            try {
//                $product = $this->handBookRelatedProductRepository->find(['id' => $product_id]);
//                $return["productsMap"][$product_id]["image"] = $product->receiveProductImages()->current()->getHttpUrl();
//                $return["productsMap"][$product_id]["title"] = $product->getTitle();
//            } catch (\Throwable $ex) {
//                return new JsonModel(["result" => false, 'error' => $ex->getMessage()]);
//            }
//        }

        return new JsonModel($return);
    }
    
    private function getBasketProductMap ($userId, $orderId = 0)
    {
        $where = new Where();
        $where->equalTo('user_id', $userId);
        if ($orderId == 0) {
            $where->notEqualTo('order_id', $orderId);
        }
        else {
            $where->equalTo('order_id', $orderId);
        }
        $columns = ['product_id'];
        $userBasketHistory = Basket::findAll(['where' => $where, 'columns' => $columns]);
        if ($userBasketHistory->count() < 1) {
            return ["result" => false, "error" => "order $orderId have not basket products ".Json::encode(['where' => $where, 'columns' => $columns]) ];
        }
        foreach ($userBasketHistory as $basketItem) {
            $product_id = $basketItem->getProductId();
            try {
                $product = $this->handBookRelatedProductRepository->find(['id' => $product_id]);
                $products[$product_id]["image"] = $product->receiveProductImages()->current()->getHttpUrl();
                $products[$product_id]["title"] = $product->getTitle();
            } catch (\Throwable $ex) {
                return ["result" => false, 'error' => $ex->getMessage()];
            }
        }
        return ["result" => true, "products" => $products ]; 
        
    }
    
    
    
    public function getUserOrderPageAction()
    {   
        $container = new Container(Resource::SESSION_NAMESPACE);
        $userId = $container->userIdentity;
        $return["result"] = false;
        $post = $this->getRequest()->getPost();
        if (!$return['order_id'] = /*"000000629"/**/ $post->orderId /**/){
            return new JsonModel($return);
        }    
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return['userId'] =  $container->userIdentity;
        $order = ClientOrder::find(['user_id' => $return['userId'], 'order_id' => $return['order_id']]);
        if (empty($order)) {
            return new JsonModel($return);
        }
        $return["order_info"] = $this->htmlProvider->orderList([$order]);
        $userBasketHistory = Basket::findAll(['where' => $where, 'columns' => $columns]);
        if ($userBasketHistory->count() < 1) {
            return new JsonModel(["result" => false]);
        }
        foreach ($userBasketHistory as $basketItem) {
            $product_id = $basketItem->getProductId();
            try {
                $product = $this->handBookRelatedProductRepository->find(['id' => $product_id]);
                $return["productsMap"][$product_id]["image"] = $product->receiveProductImages()->current()->getHttpUrl();
                $return["productsMap"][$product_id]["title"] = $product->getTitle();
            } catch (\Throwable $ex) {
                return new JsonModel(["result" => false, 'error' => $ex->getMessage()]);
            }
        }
        $productMap = $this->getBasketProductMap($userId, $return['order_id']);
        if ($productMap['result'] == false ) {
            return new JsonModel($productMap);
        }    
        $return["productsMap"] = $productMap['products'];
        
        
        $return["result"] = true;
        
        
        
        
        /*$where = new Where();
        $where->equalTo('user_id', $return['userId']);
        $where->equalTo('order_id', 0);
        $columns = ['product_id'];
        $userBasketHistory = Basket::findAll(['where' => $where, 'columns' => $columns]);
        if ($userBasketHistory->count() < 1) {
            return new JsonModel(["result" => false]);
        }
        foreach ($userBasketHistory as $basketItem) {
            $product_id = $basketItem->getProductId();
            try {
                $product = $this->handBookRelatedProductRepository->find(['id' => $product_id]);
                $return["productsMap"][$product_id]["image"] = $product->receiveProductImages()->current()->getHttpUrl();
                $return["productsMap"][$product_id]["title"] = $product->getTitle();
            } catch (\Throwable $ex) {
                return new JsonModel(["result" => false, 'error' => $ex->getMessage()]);
            }
        }*/

        return new JsonModel($return);
    }
    
    
    
    

    public function checkOrderStatusAction()
    {
        $post = $this->getRequest()->getPost();
        $return['order_id'] = $orderId = $post->orderId;
        $return['order_status'] = false;
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        if(!empty($order = ClientOrder::find(["order_id" => $orderId ]))){
            $return ['order_status'] = $order->getStatus();
            //$return ['order_status'] = 1;  //test mode switch
            $deliveryinfo  = $order->getDeliveryInfo();
            $return['delivery_info'] = (!empty($deliveryinfo))?Json::decode($deliveryinfo, Json::TYPE_ARRAY):[];
        }
        return new JsonModel($return);
    }

    public function ajaxUserDeleteAddressAction()
    {

        $post = $this->getRequest()->getPost();
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        $user = User::find(['id' => $userId]);
        $userPhone = (empty($user)) ? false : $user->getPhone();
        $userPhone = StringHelper::phoneFromNum($user->getPhone());
        if ($userPhone) {
            $this->getResponse()->setStatusCode(403);
            return;
        }
        $return['reload'] = $post->reload;
        $return['dataId'] = $post->dataId;
        $remove = UserData::remove(['where' => ['user_id' => $userId, 'id' => $return['dataId']]]);
        $return['removeresult'] = $remove->count();

        return new JsonModel($return);
    }

//
    public function ajaxUserSetDefaultAddressAction()
    {
        //$return["error"] = true;
        $post = $this->getRequest()->getPost();
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        $user = User::find(['id' => $userId]);
        if (!$return['userId']) {
            $this->getResponse()->setStatusCode(403);
            return;
        }
        $userPhone = StringHelper::phoneFromNum($user->getPhone());
        $return['reload'] = true; // $post->reload;
        $return['dataId'] = $post->dataId;
        $userData = UserData::findAll(['where' => ['user_id' => $userId, 'id' => $return['dataId']]])->current();
        if (null == $userData) {
            $return['error'] = "adress not found";
            return new JsonModel($return);
        }
        $userGeoData = $userData->getGeodata();
        $userData->setTime(time());
        $userData->persist(['user_id' => $userId, 'id' => $return['dataId']]);
        $return['updatelegalstore'] = $this->commonHelperFuncions->updateLegalStores($userGeoData);
        return new JsonModel($return);
    }

    public function ajaxBasketChangedAction()
    {
        $container = new Container(Resource::SESSION_NAMESPACE);
        $userId = $container->userIdentity;
        $whatHappened = $container->whatHappened;
        if (!empty($whatHappened['products'])) {
            $return = ["result" => true, "products" => $whatHappened['products'], "stores" => $whatHappened['stores']];
            $return ['updated'] = $this->htmlProvider->basketWhatHappenedUpdate($userId, $whatHappened['products']);
        } else {
            $return = ["result" => false];
        }
        unset($container->whatHappened);
        return new JsonModel($return);
    }

    public function basketCheckBeforeSendAction()
    {
        $userId = $this->identity();
        $user = User::find(['id' => $userId]);
        $userPhone = (empty($user)) ? false : $user->getPhone();
        if (empty($userPhone)) {
            return new JsonModel(["result" => false, "reload" => true, "reloadUrl" => "/",]);
        }
        $userData = UserData::findAll(['where' => ['user_id' => $userId], 'order' => 'timestamp DESC'])->current();
        if (empty($userData)) {
            return new JsonModel(["result" => false, "reload" => true, "reloadUrl" => "/"]);
        }
        $userGeoData = $userData->getGeodata();
        $return['updatelegalstore'] = $this->commonHelperFuncions->updateLegalStores($userGeoData);
        $post = $this->getRequest()->getPost();
        $param['basketUserId'] = $post->userIdentity;
        $param['userId'] = $userId;
        $param['postedProducts'] = $post->products;

        $where = new Where();
        $where->equalTo('user_id', $userId);
        $where->equalTo('order_id', 0);
        $columns = ['product_id', 'total', 'price'];
        $basket = Basket::findAll(['where' => $where, 'columns' => $columns]);
        if (!empty($basket)) {
            $return = $this->htmlProvider->basketCheckBeforeSendService($param, $basket);
            return new JsonModel($return);
        }
        return new JsonModel(['result' => false, "reload" => true, "reloadUrl" => "/"]);
    }
   
    public function addToFavoritesAction()
    {
        if (!$userId = $this->identity()) {
            $this->getResponse()->setStatusCode(403);
            return ; //$this->redirect()->toRoute('home');
        }
       //$productId ="11111111111"; 
       /**/
        if (empty($productId = $this->getRequest()->getPost()->productId)) {
           return new JsonModel(['result' => false, "description" => " product undefinded "]);
       }
       /**/
       $favoritesItem = ProductFavorites::findFirstOrDefault(['user_id' => $userId, 'product_id' => $productId]);
       $favoritesItem->setUserId($userId)->setProductId($productId)->setTime(time())->persist(['user_id' => $userId, 'product_id' => $productId]);
       
       return new JsonModel(['result' => true, "description" => "product $productId added to favorites", 'lable' => Resource::REMOVE_FROM_FAVORITES]);
       
    }
    
    public function removeFromFavoritesAction()
    {
       if (!$userId = $this->identity()) {
            $this->getResponse()->setStatusCode(403);
            return ; //$this->redirect()->toRoute('home');
        }
       if (empty($productId = $this->getRequest()->getPost()->productId)) {
           return new JsonModel(['result' => false, "description" => " product undefinded "]);
       }  
       ProductFavorites::remove(['where' => ['user_id' => $userId, 'product_id' => $productId]]);
       return new JsonModel(['result' => true, "description" => "product $productId removed from favorites",  'lable' => Resource::ADD_TO_FAVORITES]);
    }
    
    public function getClientFavoritesAction()
    {
        if (!$userId = $this->identity()) {
            $this->getResponse()->setStatusCode(403);
            return ; //$this->redirect()->toRoute('home');
        }
        $favProducts = ProductFavorites::findAll(["where" => ['user_id' => $userId], "order" => "timestamp desc"]);
        if ($favProducts->count() < 1){
            return new JsonModel([]);
        }
        /*$productsId=[];
        foreach ($favProducts as $favProduct)
        {
            $productsId[] = $favProduct->getProductId();
        }
        $products = $this->handBookRelatedProductRepository->findAll(["where"=>["id" => $productsId]]); 
        return new JsonModel($this->commonHelperFuncions->getProductCardArray($products, $userId));*/
        $products = [];
        foreach ($favProducts as $favProduct){
            if (null != $product = $this->handBookRelatedProductRepository->find(["id" => $favProduct->getProductId()])){
                $products[] = $this->commonHelperFuncions->getProductCardArray([$product], $userId);   
            }
        }
        return new JsonModel($products);
    }
    
    private function mySqlSort(array $input)
    {
        $sql = new Sql();
        
    }
    
    public function getClientHistoryAction()
    {
        if (!$userId = $this->identity()) {
            $this->getResponse()->setStatusCode(403);
            return ; //$this->redirect()->toRoute('home');
        }
        $favProducts = ProductHistory::findAll(["where" => ['user_id' => $userId], "order" => "timestamp desc"]);
        if ($favProducts->count() < 1){
            return new JsonModel([]);
        }
        $products = [];
        foreach ($favProducts as $favProduct)
        {
            if (null != $product = $this->handBookRelatedProductRepository->find(["id" => $favProduct->getProductId()])){
                $products[] = $this->commonHelperFuncions->getProductCardArray([$product], $userId);   
                
            }; 
        }
        //$this->handBookRelatedProductRepository->findAll(["where"=>["id" => $productsId], 'order' => ['id' => $productsId ]]); 
        return new JsonModel($products);
    }
    
    /*
     *  промежуточный скрипт для http://api4.searchbooster.io 
     * @return json
     */
    public function searchBoosterApiAction ()
    {
         $json = file_get_contents(str_replace("/get-search-booster-api", "", "http://api4.searchbooster.io".$_SERVER["REQUEST_URI"]));
         $return =(!empty($json)) ? Json::decode($json, Json::TYPE_ARRAY) : [];
         return new JsonModel($return);
     }

     
    public function addToBasketAction()
    {
        if (!$userId = $this->identity()) {
            $this->getResponse()->setStatusCode(403);
            return ; //$this->redirect()->toRoute('home');
        }
        $return = ["error" => true, "count" => 0];
        $return['total'] = $return['count'] = 0;
        $post = $this->getRequest()->getPost();
        if (empty($return['productId'] = $productId = $post->product))
        {
            return new JsonModel($return);
        }
             $return['error'] = false;
        $basketItem = Basket::findFirstOrDefault(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);
        $basketItemTotal = (int) $basketItem->getTotal();
        $basketItem->setUserId($userId);
        $basketItem->setProductId($productId);
        $productadd = $this->handBookRelatedProductRepository->findAll(['where' => ['id' => $productId]])->current();
        $productaddPrice = (int) $productadd->getPrice();
        $basketItem->setPrice($productaddPrice);
        $basketItem->setTotal($basketItemTotal + 1);
        $basketItem->persist(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);

        $where = new Where();
        $where->equalTo('user_id', $userId);
        $where->equalTo('order_id', 0);
        $columns = ['product_id', 'order_id', 'total'];
        $basket = Basket::findAll(['where' => $where, 'columns' => $columns]);
        foreach ($basket as $b) {
            if ($pId = $b->productId) {
                $product = $this->productRepository->find(['id' => $pId]);
                $return['products'][] = [
                    "id" => $pId,
                    "name" => $product->getTitle(),
                    "count" => $b->total,
                    'image' => $this->productImageRepository->findFirstOrDefault(["product_id" => $pId])->getHttpUrl(),
                ];
                $return['total'] += $b->total;
                $return['count']++;
            }
        }
        return new JsonModel($return);
    }

    /*public function userAuthAction()
    {
        $password = $smsCode = "7777"; //костыль
        $return = ["error" => true, "message" => Resource::ERROR_MESSAGE, "isUser" => false, "username" => ""];
        $post = $this->getRequest()->getPost();
        $return['phone'] = StringHelper::phoneToNum($post->userPhone); // $this->phoneToNum($post->userPhone);
        $return['name'] = $post->userNameInput;
        $code = $post->userSmsCode;
        $container = new Container(Resource::SESSION_NAMESPACE);
        if (!$return['phone']) {
              $return["message"] .= Resource::ERROR_INPUT_PHONE_MESSAGE;
        } else {
            $user = $this->userRepository->findFirstOrDefault(["phone" => $return['phone']]);
            if ($user and $userId = $user->getId()) {
                $return['userId'] = $userId;
                $return["isUser"] = true;
                if ($post->userPass == $password) {
                    $container->userIdentity = $return['userId'];
                    $return["error"] = false;
                }
                $return["message"] = Resource::ERROR_INPUT_PASSWORD_MESSAGE
                        . "($password)"
                ;
                $return["username"] = $user->getName();
            } else {
                if ($return['name'] and $code == $smsCode) {

                    $user = $this->userRepository->findFirstOrDefault(["id" => $container->userIdentity]);
                    $user->setName($return['name']);
                    $user->setPhone($return['phone']);
                    $this->userRepository->persist($user, ['id' => $user->getId()]);
                    $return["error"] = false;
                }
                $return["message"] = Resource::ERROR_INPUT_NAME_SMS_MESSAGE;  //это телефонный номер  юзера
            }
        }
        $return['post'] = $post;
        return new JsonModel($return);
    }*/

    public function calculateBasketItemAction()
    {
        $post = $this->getRequest()->getPost();
        if (!$userId = $this->identity()){
            return new JsonModel(["error" => true, "errorMessage" => "user not found"]);
        }
        if (!$return['productId'] = $productId = $post->product){
            return new JsonModel(["error" => true, "errorMessage" => "product not found"]);
        }
        $product = $this->handBookRelatedProductRepository->findAll(['where' => ['id' => $productId]])->current();
        if (null == $product  or !$productPrice = (int) $product->getPrice() or! $productCount = (int) $post->count   or $productCount < 1) {
            return new JsonModel(["error" => true, "errorMessage" => "product price error"]);
        }
        $basketItem = Basket::findFirstOrDefault(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);
        $basketItem->setTotal($productCount);
        $basketItem->persist(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);
        $return['totalNum'] = (int) $productPrice * $productCount;
        $return['totalFomated'] = number_format($return['totalNum'] / 100, 0, ',', '&nbsp;');
        return new JsonModel($return);
    }

    public function basketOrderMergeAction()
    {
        $param = (!empty($delivery_params = Setting::find(['id' => 'delivery_params']))) ? Json::decode($delivery_params->getValue(), Json::TYPE_ARRAY) : [];
        if (!$userId = $this->identity()) {
            $this->getResponse()->setStatusCode(403);
            return ; //$this->redirect()->toRoute('home');
        }
        $post = $this->getRequest()->getPost();
        $timepoint = $post->timepoint;
        $selectedtimepoint[0][$timepoint[0]] = " selected ";
        $selectedtimepoint[1][$timepoint[1]] = " selected ";
        $return = $this->htmlProvider->basketMergeData($post, $param);
        $view = new ViewModel([
            'ordermerge' => $post->ordermerge,
            'timeClose' => $return['timeClose'],
            'countStors' => $return["count"],
            'hourPrice' => $return["hourPrice"],
            'hour3Price' => $return["hour3Price"],
            'select1hour' => $return["select1hour"],
            'select3hour' => $return["select3hour"],
            'selectedtimepoint' => $selectedtimepoint,
            'timepointtext1' => $post->timepointtext1,
            'timepointtext3' => $post->timepointtext3,
            'printr' => "<pre>" . print_r($post, true) . "</pre>",
        ]);
        $view->setTemplate('application/common/basket-order-merge');
        return $view->setTerminal(true);
    }

    public function basketPayCardInfoAction()
    {
        if (!$userId = $this->identity()){
            return new JsonModel(["error" => true, "errorMessage" => "user not found"]);
        }
        $userPaycards = UserPaycard::findAll(['where' => ["user_id" => $userId], "order" => "timestamp desc"]);
        $paycards =($userPaycards->count())?$userPaycards:null;
        $cardInfo = $this->htmlProvider->getUserPayCardInfoService($paycards);
        
        $post = $this->getRequest()->getPost();
        $paycard = $post->paycard;
        $view = new ViewModel([
            'paycard' => $paycard,
            'cardinfo' => $cardInfo,
            'default' => $post->cardinfo,
        ]);
        $view->setTemplate('application/common/basket-pay-card');
        return $view->setTerminal(true);
    }

    public function basketPayInfoAction()
    {
         if (!$userId = $this->identity()) {
            $this->getResponse()->setStatusCode(403);
            return ; 
        }
        $user = $this->userRepository->find(['id' => $userId]);
        $basketUser['phone'] = $user->getPhone();
        $basketUser['name'] = $user->getName();
        $userData = $user->getUserData();
        if ($userData->count() > 0) {
            $basketUser['address'] = $userData->current()->getAddress();
        }
        $param = (!empty($delivery_params = Setting::find(['id' => 'delivery_params'])))?Json::decode($delivery_params->getValue(), Json::TYPE_ARRAY):[];
        $post = $this->getRequest()->getPost();
        $row = $this->htmlProvider->basketPayInfoData($post, $param);
        $timeDelevery = (!$post->ordermerge) ? $post->timepointtext1 : $post->timepointtext3;
        $row['payEnable'] = ($row['total'] > 0 and ($row['countSelfdelevery'] or ($row['countDelevery'] /* and $timeDelevery */))) ? true : false;
        
        if ($post->cardinfo) {
            $userPaycards = UserPaycard::findAll(["where" => ["user_id" => $userId, "card_id"=>$post->cardinfo]]);
            $cardUpdate = $userPaycards->current();
            $cardInfo = $cardUpdate ->getPan();
            $cardUpdate->setTime(time());
            $cardUpdate->persist(["user_id" => $userId, "card_id"=>$post->cardinfo]);
        }
        
        $view = new ViewModel([
            "payEnable" => $row["payEnable"],
            "textDelevery" => $row["textDelevery"],
            'priceDelevery' => $param['hourPrice'],
            'ordermerge' => $post->ordermerge,
            'priceDeleveryMerge' => $param['mergePrice'],
            'priceDeleveryMergeFirst' => $param['mergePriceFirst'],
            'countDelevery' => $row["countDelevery"],
            'countDeleveryText' => $row["countDeleveryText"],
            'priceDelevery' => $row['priceDelevery'],
            'addressDelevery' => StringHelper::cutAddressCity($basketUser['address']),
            'priceSelfdelevery' => 0,
            'basketpricetotalall' => $row['basketpricetotalall'],
            'post' => $row['post'],
            'productcount' => $row['count'],
            'producttotal' => $row['total'],
            'countSelfdelevery' => $row['countSelfdelevery'],
            'storeAdress' => $row["storeAdress"],
            "cardinfo" => $cardInfo,
            'paycard' => $post->paycard,
            'timeDelevery' => $timeDelevery,
        ]);
        $view->setTemplate('application/common/basket-payinfo');
        return $view->setTerminal(true);
    }

    public function previewAction()
    {
        $this->layout()->setTemplate('layout/preview');
        $categories = $this->categoryRepository->findAllCategories();
        return new ViewModel([
            'menu' => $categories
        ]);
    }

    public function ajaxGetLegalStoreAction()
    {
        $post = $this->getRequest()->getPost();
        if (!$json = $post->value){
            return new JsonModel(NULL);
        }
        try {
            $TMP = Json::decode($json);
        } catch (LaminasJsonRuntimeException $e) {
            return new JsonModel(["result" => false, "error" => $e->getMessage()]);
        }
        $ob = $TMP->data;
        if (!$ob->house) {
            return new JsonModel(["result" => false, "error" => Resource::USER_ADDREES_ERROR_MESSAGE]);
        }
        $return = $this->commonHelperFuncions->updateLegalStores($json);
        return new JsonModel($return);
    }

    public function ajaxAddUserAddressAction()
    {
        if (!$userId = $this->identity()) {
            $this->getResponse()->setStatusCode(403);
            return ; 
        }
        $user = $this->userRepository->find(['id' => $userId]);
        $post = $this->getRequest()->getPost();
        $return["user"] = $userId;
        if ($userId and $post->address and $post->dadata) {
            $return["error"] = "Successfull! ";
            $return["ok"] = "Address fixed as: {$post->address}";
            $userData = new UserData();
            $userData->setUserId($userId);
            $userData->setAddress($post->address);
            $userData->setGeodata($post->dadata);
            $userData->setTime(time());
            try {
                $user->setUserData([$userData]);
            } catch (InvalidQueryException $e) {
                $return['error'] = $e->getMessage();
            }
        } 
        else {
            $return["error"] = "Error! ";
        }
        return new JsonModel($return);
    }

    public function ajaxSetUserAddressAction()
    {
        if (!$userId = $this->identity()) {
            $this->getResponse()->setStatusCode(403);
            return ; 
        }
        $user = $this->userRepository->find(['id' => $userId]);
        $return["userAddress"] = $this->htmlProvider->writeUserAddress($user);
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return["legalStore"] = $container->legalStore;
        return new JsonModel($return);
    }

    public function unsetFilterForCategoКyAction()
    {
        $post = $this->getRequest()->getPost();
        $category_id = $post->category_id;
        $container = new Container(Resource::SESSION_NAMESPACE);
        unset($container->filtrForCategory[$category_id]);
    }

    /**
     * Return true if given product matches
     * the specified characteristics
     *
     * @param HandbookRelatedProduct $product
     * @param array $characteristics
     * @return bool
     */
    private function matchProduct(HandbookRelatedProduct /* \Application\Model\Entity\Product */ $product, array $characteristics): bool
    {
        $flags = [];
        foreach ($characteristics as $key => $value) {
            $found = $this->productCharacteristicRepository->find(['characteristic_id' => $key, 'product_id' => $product->getId()]);
            if (null == $found) {
                $flags[$key] = false;
                continue;
            }
            $type = $found->getType();
            switch ($type) {
                case CharacteristicRepository::INTEGER_TYPE:
                    reset($value);
                    list($left, $right) = explode(';', current($value));
                    //list($left, $right) = explode(';', $value[0]);
                    $flags[$key] = !($found->getValue() < $left || $found->getValue() > $right);
                    break;
                case CharacteristicRepository::BOOL_TYPE:
                    $flags[$key] = ($found->getValue() == $value);
                    break;
                default:
                    $flags[$key] = in_array($found->getValue(), $value);
                    break;
            }
        }
        foreach ($flags as $f) {
            if (!$f) {
                return false;
            }
        }
        return true;
    }

    /**
     * Return where clause to filter products by price and category
     *
     * @param array $params
     * @return Where
     */
    private function getWhere($params): Where
    {
        $category_id = $params['category_id'];
        $categoryTree = $this->categoryRepository->findCategoryTree($category_id, [$category_id]);
        $where = new Where();
        list($low, $high) = explode(';', $params['priceRange']);
        $where->lessThanOrEqualTo('price', $high)->greaterThanOrEqualTo('price', $low);
        $where->in('category_id', $categoryTree);
        $characteristics = null == $params['characteristics'] ? [] : $params['characteristics'];    
        $charsId = array_keys($characteristics);
        return $this->subQueryWhere($charsId, $where) ;
    }
    
    private function subQueryWhere ($charsId, $where): Where 
    {
        $allCahrs = ProductCharacteristic::findAll(['characteristic_id' => $charsId ]);
        if ($allCahrs->count() < 1) {
            return $where;
        }
        $columns = ['product_id'];
        $subWhere = new Where();
       
        foreach   ($allCahrs  as $found){
            $type = $allCahrs->getType();
            $value = $found->getValue();
            
            switch ($type) {
                case CharacteristicRepository::INTEGER_TYPE:
                    reset($value);
                    list($left, $right) = explode(';', current($value));
                    $subWhere->lessThanOrEqualTo('value', $right)->greaterThanOrEqualTo('value', $left);

                    //list($left, $right) = explode(';', $value[0]);
                    //$flags[$key] = !($found->getValue() < $left || $found->getValue() > $right);
                    break;
                case CharacteristicRepository::BOOL_TYPE:
                   $subWhere->equalTo('value', $value);
                    break;
                default:
                    $subWhere->in('value', $value);
                    break;
            }
            
        
        }
        $subQuery = ProductCharacteristic::findAll(["where" =>  $subWhere, "columns"=>$columns ])->toArray();
       //exit (print_r($subQuery));
        
        $where->in('product_id', $subQuery);    
        return $where;
    }
    
    
    

    /**
     * Return where clause for qwery
     *
     * @param array $params
     * @return Where
     */
    private function getWhereCategories($params): Where
    {
        $where = new Where();
        $where->in('category_id', $params);
        return $where;
    }

/**
     * Return filtered HandbookRelatedProduct filtered products
     *
     * @param array $params
     * @return HandbookRelatedProduct[]
     */
    private function getProductsCategories ($params)
    {

        $params['where'] = $this->getWhereCategories($params);
        $products = $this->handBookRelatedProductRepository->findAll($params);
        $filteredProducts = $this->commonHelperFuncions->getProductCardArray($products, $this->identity());
        return $filteredProducts;
    }

    /**
     * Return filtered HandbookRelatedProduct filtered products
     *
     * @param array $params
     * @return HandbookRelatedProduct[]
     */
    private function getProducts($params)
    {
        $this->prepareCharacteristics($params['characteristics']);
        if (empty($params['priceRange'])) {
            $params['priceRange'] = '0;' . PHP_INT_MAX;
        }
        unset($params['offset']);
        unset($params['limit']);
        $params['where'] = $this->getWhere($params);
        //$params['order'] = ['price ASC'];
        $products = $this->handBookRelatedProductRepository->findAll($params);

        $filteredProducts = [];
        foreach ($products as $product) {
            $characteristics = null == $params['characteristics'] ? [] : $params['characteristics'];
            $matchResult = $this->matchProduct($product, /* $params['characteristics'] */ $characteristics);
            if ($matchResult && !isset($filteredProducts[$product->getId()])) {
                $filteredProducts[$product->getId()] = $product;
            }
        }
        return $filteredProducts;
    }

    private function getProductCards($params)
    {
        $this->prepareCharacteristics($params['characteristics']);
        //$this-\Laminas\Log\Writer\Mail::
        if (empty($params['priceRange'])) {
            $params['priceRange'] = '0;' . PHP_INT_MAX;
        }
        unset($params['offset'], $params['limit']);
        $container = new Container(Resource::SESSION_NAMESPACE);
        //$return['legalStores'] = 
        $legalStores = $container->legalStore;
        
        $params['where'] = $this->getWhere($params);
        $products = $this->handBookRelatedProductRepository->findAll($params);
        $filteredProducts = [];
        $store =[];
        $available = false; 
        
        foreach ($products as $product) {
            $characteristics = null == $params['characteristics'] ? [] : $params['characteristics'];
            $matchResult = $this->matchProduct($product, /* $params['characteristics'] */ $characteristics);
            if ($matchResult && !isset($filteredProducts[$product->getId()])) {
                
                $provider = $product->getProvider();
                $strs = $provider->getStores();
                $product->getProvider();
                $store =[];
                $available = false; 
                foreach  ($strs as $s){
                    if (!empty($legalStores[$s->getId()])) {
                       $available = true; 
                       $store[] =  $s->getId();
                     }
                }
                $oldPrice = 0;
                $price = $product->getPrice();
                $discont = $product->getDiscount();
                if ($discont > 0 ){
                    $oldPrice =  $price;
                    $price = $oldPrice - ($oldPrice * $discont /100);
                }
                $filteredProducts[$product->getId()] = [
                    "reserve" => $product->receiveRest($store),
                    "price" => $price,
                    "title" => $product->getTitle(),
                    'available' =>  $available,
                    'oldprice' => $oldPrice,
                    //'stores' => $productStores,
                    "discount" => $product->getDiscount(),
                    "image" => $product->receiveFirstImageObject()->getHttpUrl(),
                    'isFav' => $this->commonHelperFuncions->isInFavorites($product->getId(), $this->identity()),
                ];
            }
        }
        return $filteredProducts;
    }

    private function prepareCharacteristics(&$characteristics)
    {
        if (!$characteristics) {
            return;
        }
        foreach ($characteristics as $key => &$value) {
            foreach ($value as &$v) {
                if (empty($v)) {
                    $v = '0;' . PHP_INT_MAX;
                }
            }
        }
    }

//    private function getProducts1($params)
//    {
//        $where = new Where();
//        $where->greaterThan('rest', 0);
//        $rests = $this->stockBalanceRepository->findAll(['where' => $where]);
//        $products = [];
//        $params['where'] = $this->getWhere($params);
//        foreach($rests as $rest) {
//            $productId = $rest->getProductId();
//            $product = $this->handBookRelatedProductRepository->findFirstOrDefault(['id' => $productId]);
//            $matchResult = $this->matchProduct($product, $params['characteristics']);
//            if ($matchResult && !isset($products[$product->getId()]) ) {
//                $products[$productId] = $product;
//            }
//        }
//        return $products;
//    }

    public function setFilterForCategoryAction()
    {

        $post = $this->getRequest()->getPost()->toArray();
        $products = $this->getProducts($post);
        return (new ViewModel(['products' => $products]))->setTerminal(true);
    }



    public function getProductCategoriesAction()
    {
        $post = $this->getRequest()->getPost();
        $categoryId = $post->categoryId;
        //$categoryId = "000000001";
        if (empty($params = Setting::find(['id' => 'main_menu']))){
            return new JsonModel([]);
        }
        $categories = Json::decode( $params->getValue(), Json::TYPE_ARRAY);
        $category = $categories[$categoryId]["categories"];
        foreach ($category as $item){
            $param[] = $item["id"];
        }
        $products = $this->getProductsCategories($param);
        return new JsonModel($products);
     }

    public function getFiltredProductForCategoryJsonAction()
    {

        $post = $this->getRequest()->getPost()->toArray();
        $products = $this->getProductCards($post);
        return new JsonModel(["products" => $products]);
    }

    public function providerAction()
    {
        $id = $this->params()->fromRoute('id', '');
        $this->layout()->setTemplate('layout/mainpage');
        $categories = $this->categoryRepository->findAllCategories("", 0, $id);
        $providers = $this->providerRepository->findAll(['table' => 'provider', 'limit' => 100, 'order' => 'id ASC', 'offset' => 0]);
        return new ViewModel([
            "providers" => $providers,
            "catalog" => $categories,
        ]);
    }

}
