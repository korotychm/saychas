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
//use Application\Model\Repository\CharacteristicRepository;
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
use Application\Model\Entity\StockBalance;
use Application\Model\Entity\Brand;
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
use Application\Helper\ArrayHelper;
use Application\Helper\StringHelper;

class AjaxController extends AbstractActionController {

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
            HtmlProviderService $htmlProvider,
            UserRepository $userRepository,
            AuthenticationService $authService,
            ProductCharacteristicRepositoryInterface $productCharacteristicRepository, BasketRepositoryInterface $basketRepository, ProductImageRepositoryInterface $productImageRepository/* ,
              SessionContainer $sessionContainer */, CommonHelperFunctionsService $commonHelperFuncions) {
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
        $this->entityManager->initRepository(ProductCharacteristic::class);
        $this->entityManager->initRepository(StockBalance::class);
        $this->entityManager->initRepository(Brand::class);
    }

    /**
     * return Top brands
     *
     * @return JSON
     */
    public function getBrandsTopAction() 
    {
        $count_columns = new \Laminas\Db\Sql\Expression("count(`product_id`) as `count`, `product_id` as product_id");
        $productsTop = ProductHistory::findAll(['columns' => [$count_columns], 'group' => ['product_id'], 'having' => ['count > 1'], 'group' => ['product_id'], 'limit' => $limit,])->toArray();
        $productsId = ArrayHelper::extractId($productsTop);
        $productWhere = new Where();
        $productWhere->in("id", $productsId);
        $products = $this->productRepository->findAll(["where" => $productWhere, "columns" => ["brand_id"], "group" => "brand_id"])->toArray();
        $brandsId = ArrayHelper::extractId($products, "brand_id");
        //return new JsonModel($brandsId);
        $brands = Brand::findAll(["where" => ["id" => $brandsId], 'limit' => Resource::SQL_LIMIT_BRAND_SLIDER])->toArray();
        foreach ($brands as $brand) {
            $return[] = $brand;
        }

        return new JsonModel($return);
    }

    public function ajaxGetBasketJsonAction() 
    {
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        if (empty($return['userId'])) {
            $this->getResponse()->setStatusCode(403);
            return;
        }
        $return['basket'] = [];
        $basket = Basket::findAll(['user_id' => $userId, 'order_id' => "0"]);
        if (empty($basket)) {
            return new JsonModel($return);
        }
        foreach ($basket as $basketItem) {
            $return['basket'][$basketItem->getProductId()] = ["id" => $basketItem->getProductId(), "total" => $basketItem->getTotal(), "price" => $basketItem->getPrice(), "discount" => $basketItem->getDiscount(),];
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
        Basket::remove(['where' => ['user_id' => $userId, 'product_id' => $productId]]);

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
        $products = $this->productRepository->findAll(['where' => ['category_id' => $categoryTree], 'columns' => ['id'], "group" => ['id']])->toArray();
        $productsId = ArrayHelper::extractId($products, 'id');
        $return["rangeprice"] = $this->priceRepository->findMinMaxPrice($productsId);
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
        if ($productMap['result'] == false) {
            return new JsonModel($productMap);
        }
        $return["productsMap"] = $productMap['products'];

        return new JsonModel($return);
    }

    private function getBasketProductMap($userId, $orderId = 0) 
    {
        $products = [];
        $where = new Where();
        $where->equalTo('user_id', $userId);
        if ($orderId == 0) {
            $where->notEqualTo('order_id', $orderId);
        } else {
            $where->equalTo('order_id', $orderId);
        }
        $columns = ['product_id'];
        $userBasketHistory = Basket::findAll(['where' => $where, 'columns' => $columns]);

        foreach ($userBasketHistory as $basketItem) {
            $product_id = $basketItem->getProductId();
            try {
                $product = $this->handBookRelatedProductRepository->find(['id' => $product_id]);
                $products[$product_id] = ["image" => $product->receiveProductImages()->current()->getHttpUrl(), "title" => $product->getTitle()];
            } catch (\Throwable $ex) {
                return ["result" => false, 'error' => $ex->getMessage()];
            }
        }
        return empty($products) ? ["result" => false, "error" => "order $orderId have not basket products " . Json::encode(['where' => $where, 'columns' => $columns])] : ["result" => true, "products" => $products];
    }

    public function getUserOrderPageAction() 
    {
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        $return["result"] = false;
        $post = $this->getRequest()->getPost();
        if (!empty($return['order_id'] = $post->orderId)) {
            return new JsonModel($return);
        }
        $order = ClientOrder::find(['user_id' => $return['userId'], 'order_id' => $return['order_id']]);
        if (empty($order)) {
            return new JsonModel($return);
        }
        $return["order_info"] = $this->htmlProvider->orderList([$order]);
        $productMap = $this->getBasketProductMap($userId, $return['order_id']);
        if ($productMap['result'] == false) {
            return new JsonModel($productMap);
        }
        $return["productsMap"] = $productMap['products'];
        $return["result"] = true;

        return new JsonModel($return);
    }

    public function checkOrderStatusAction() 
    {
        $post = $this->getRequest()->getPost();
        $return['order_id'] = $orderId = $post->orderId;
        $return['order_status'] = false;
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        if (!empty($order = ClientOrder::find(["order_id" => $orderId]))) {
            $return ['order_status'] = $order->getStatus();
            //$return ['order_status'] = 1;  //test mode switch
            $deliveryinfo = $order->getDeliveryInfo();
            $return['delivery_info'] = (!empty($deliveryinfo)) ? Json::decode($deliveryinfo, Json::TYPE_ARRAY) : [];
        }

        return new JsonModel($return);
    }

    public function ajaxUserDeleteAddressAction() 
    {
        $post = $this->getRequest()->getPost();
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        $user = User::find(['id' => $userId]);
        if (empty($user) or empty($user->getPhone())) {
            $this->getResponse()->setStatusCode(403);
            return;
        }
        $return['reload'] = $post->reload;
        $return['dataId'] = $post->dataId;
        $remove = UserData::remove(['where' => ['user_id' => $userId, 'id' => $return['dataId']]]);
        $return['removeresult'] = $remove->count();

        return new JsonModel($return);
    }

    public function ajaxUserSetDefaultAddressAction() 
    {
        $post = $this->getRequest()->getPost();
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        if (!$return['userId']) {
            $this->getResponse()->setStatusCode(403);
            return;
        }
        $return['reload'] = true;
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
        //$userPhone = (empty($user)) ? false : $user->getPhone();
        if (empty($user) or empty($user->getPhone())) {
            return new JsonModel(["result" => false, "reload" => true, "reloadUrl" => "/",]);
        }
        $userData = UserData::findAll(['where' => ['user_id' => $userId], 'order' => 'timestamp DESC'])->current();
        if (empty($userData)) {
            return new JsonModel(["result" => false, "reload" => true, "reloadUrl" => "/"]);
        }
        //$userGeoData = $userData->getGeodata();
        $return['updatelegalstore'] = $this->commonHelperFuncions->updateLegalStores($userData->getGeodata());
        $post = $this->getRequest()->getPost();
        //$param = ['basketUserId' => $post->userIdentity, 'userId' => $userId, 'postedProducts' => $post->products];

        $where = new Where();
        $where->equalTo('user_id', $userId)->equalTo('order_id', 0);
        $columns = ['product_id', 'total', 'price'];
        $basket = Basket::findAll(['where' => $where, 'columns' => $columns]);
        if (!empty($basket)) {
            $return = $this->htmlProvider->basketCheckBeforeSendService(['basketUserId' => $post->userIdentity, 'userId' => $userId, 'postedProducts' => $post->products], $basket);
            return new JsonModel($return);
        }

        return new JsonModel(['result' => false, "reload" => true, "reloadUrl" => "/"]);
    }

    public function addToFavoritesAction() 
    {
        if (!$userId = $this->identity()) {

            return $this->getResponse()->setStatusCode(403);
        }
        if (empty($productId = $this->getRequest()->getPost()->productId)) {

            return new JsonModel(['result' => false, "description" => " product undefinded "]);
        }
        $favoritesItem = ProductFavorites::findFirstOrDefault(['user_id' => $userId, 'product_id' => $productId]);
        $favoritesItem->setUserId($userId)->setProductId($productId)->setTime(time())->persist(['user_id' => $userId, 'product_id' => $productId]);

        return new JsonModel(['result' => true, "description" => "product $productId added to favorites", 'lable' => Resource::REMOVE_FROM_FAVORITES]);
    }

    public function removeFromFavoritesAction() 
    {
        if (!$userId = $this->identity()) {
            
            return   $this->getResponse()->setStatusCode(403);
        }
        if (empty($productId = $this->getRequest()->getPost()->productId)) {
            
            return new JsonModel(['result' => false, "description" => " product undefinded "]);
        }
        ProductFavorites::remove(['where' => ['user_id' => $userId, 'product_id' => $productId]]);

        return new JsonModel(['result' => true, "description" => "product $productId removed from favorites", 'lable' => Resource::ADD_TO_FAVORITES]);
    }

//    public function getClientFavoritesAction() {
//        if (!$userId = $this->identity()) {
//            $this->getResponse()->setStatusCode(403);
//            return;
//        }
//        $favProducts = ProductFavorites::findAll(["where" => ['user_id' => $userId], "order" => "timestamp desc"]);
//        $productsId = ArrayHelper::extractId($favProducts);
//        $product = $this->handBookRelatedProductRepository->findAll(["where" => ["id" => $productsId]]);
//        return new JsonModel(["products" => $this->commonHelperFuncions->getProductCardArray($product, $userId)]);
//    }
//
//    public function getClientHistoryAction() {
//        if (!$userId = $this->identity()) {
//            $this->getResponse()->setStatusCode(403);
//            return; //$this->redirect()->toRoute('home');
//        }
//        $favProducts = ProductHistory::findAll(["where" => ['user_id' => $userId], "order" => "timestamp desc"])->toArray();
//        $productsId = ArrayHelper::extractId($favProducts);
//        $product = $this->handBookRelatedProductRepository->findAll(["where" => ["id" => $productsId]]);
//        return new JsonModel(["products" => $this->commonHelperFuncions->getProductCardArray($product, $userId)]);
//    }

    /*
     *  промежуточный скрипт для http://api4.searchbooster.io
     * @return json
     */
    public function searchBoosterApiAction() 
    {
        $json = file_get_contents(str_replace("/get-search-booster-api", "", "http://api4.searchbooster.io" . $_SERVER["REQUEST_URI"]));
        $return = (!empty($json)) ? Json::decode($json, Json::TYPE_ARRAY) : [];

        return new JsonModel($return);
    }

    public function addToBasketAction() 
    {
        if (!$userId = $this->identity()) {
            
            return $this->getResponse()->setStatusCode(403);
        }
        $return = ["error" => true, "count" => 0];
        $return['total'] = $return['count'] = 0;
        $post = $this->getRequest()->getPost();
        if (!empty($return['productId'] = $productId = $post->product)) {
            //$return['error'] = false;
            $basketItem = Basket::findFirstOrDefault(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);
            $basketItemTotal = (int) $basketItem->getTotal();
            $basketItem->setUserId($userId);
            $basketItem->setProductId($productId);
            $productadd = $this->handBookRelatedProductRepository->findAll(['where' => ['id' => $productId]])->current();
            $productaddPrice = (int) $productadd->getPrice();
            $basketItem->setPrice($productaddPrice);
            $basketItem->setTotal($basketItemTotal + 1);
            $basketItem->persist(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);
        }
        $where = new Where();
        $where->equalTo('user_id', $userId);
        $where->equalTo('order_id', 0);
        $columns = ['product_id', 'order_id', 'total'];
        $basket = Basket::findAll(['where' => $where, 'columns' => $columns]);
        foreach ($basket as $b) {
            if (!empty($pId = $b->productId)) {
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

    public function calculateBasketItemAction() 
    {
        $post = $this->getRequest()->getPost();
        if (!$userId = $this->identity()) {
            return new JsonModel(["error" => true, "errorMessage" => "user not found"]);
        }
        if (!$return['productId'] = $productId = $post->product) {
            return new JsonModel(["error" => true, "errorMessage" => "product not found"]);
        }
        $product = $this->handBookRelatedProductRepository->findAll(['where' => ['id' => $productId]])->current();
        if (null == $product or!$productPrice = (int) $product->getPrice() or!$productCount = (int) $post->count or $productCount < 1) {
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
            
            return $this->getResponse()->setStatusCode(403);
        }
        $post = $this->getRequest()->getPost();
        $timepoint = $post->timepoint;
        $selectedtimepoint = [];
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
        if (!$userId = $this->identity()) {
            return new JsonModel(["error" => true, "errorMessage" => "user not found"]);
        }
        $userPaycards = UserPaycard::findAll(['where' => ["user_id" => $userId], "order" => "timestamp desc"]);
        $paycards = ($userPaycards->count()) ? $userPaycards : null;
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
            return;
        }
        $user = $this->userRepository->find(['id' => $userId]);
        $basketUser['phone'] = $user->getPhone();
        $basketUser['name'] = $user->getName();
        $userData = $user->getUserData();
        if ($userData->count() > 0) {
            $basketUser['address'] = $userData->current()->getAddress();
        }
        $param = (!empty($delivery_params = Setting::find(['id' => 'delivery_params']))) ? Json::decode($delivery_params->getValue(), Json::TYPE_ARRAY) : [];
        $post = $this->getRequest()->getPost();
        $row = $this->htmlProvider->basketPayInfoData($post, $param);
        $timeDelevery = (!$post->ordermerge) ? $post->timepointtext1 : $post->timepointtext3;
        $row['payEnable'] = ($row['total'] > 0 and ($row['countSelfdelevery'] or ($row['countDelevery'] /* and $timeDelevery */))) ? true : false;

        if ($post->cardinfo) {
            $userPaycards = UserPaycard::findAll(["where" => ["user_id" => $userId, "card_id" => $post->cardinfo]]);
            $cardUpdate = $userPaycards->current();
            $cardInfo = $cardUpdate->getPan();
            $cardUpdate->setTime(time());
            $cardUpdate->persist(["user_id" => $userId, "card_id" => $post->cardinfo]);
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

        return new ViewModel(['menu' => '',]);
    }

    public function ajaxGetLegalStoreAction() 
    {
        $post = $this->getRequest()->getPost();
        if (!$json = $post->value) {
            return new JsonModel(null);
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
            return;
        }
        $user = $this->userRepository->find(['id' => $userId]);
        $post = $this->getRequest()->getPost();
        $return["user"] = $userId;
        if ($userId and $post->address and $post->dadata) {
            $return["error"] = "Successfull! ";
            $return["ok"] = "Address fixed as: {$post->address}";
            $userData = new UserData();
            $userData->setUserId($userId)->setAddress($post->address)->setGeodata($post->dadata)->setTime(time());
            try {
                $user->setUserData([$userData]);
            } catch (InvalidQueryException $e) {
                $return['error'] = $e->getMessage();
            }
        return new JsonModel($return);
        } 
        return new JsonModel(["error" => "Error! "]);
    }

    public function ajaxSetUserAddressAction() 
    {
        if (!$userId = $this->identity()) {
            $this->getResponse()->setStatusCode(403);
            return;
        }
        $user = $this->userRepository->find(['id' => $userId]);
        $return["userAddress"] = $this->htmlProvider->writeUserAddress($user);
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return["legalStore"] = $container->legalStore;

        return new JsonModel($return);
    }
    //    private function prepareCharacteristics(&$characteristics) {
//        if (!$characteristics) {
//            return;
//        }
//        foreach ($characteristics as $key => &$value) {
//            if ($value) {
//                foreach ($value as &$v) {
//                    if (empty($v)) {
//                        $v = '0;' . PHP_INT_MAX;
//                    }
//                }
//            }
//        }
//    }


}
