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
use Application\Model\Entity\ProductRating;
use Application\Model\Entity\ProductUserRating;
use Application\Model\Entity\Delivery;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
//use Application\Model\Repository\CharacteristicRepository;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
//use Application\Model\Entity\HandbookRelatedProduct;
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
use Application\Model\Entity\Provider;
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
use Application\Helper\MathHelper;

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
    private $repoRating;

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
        $this->entityManager->initRepository(ProductCharacteristic::class);
        $this->entityManager->initRepository(StockBalance::class);
        $this->entityManager->initRepository(Brand::class);
        $this->entityManager->initRepository(ProductRating::class);
        $this->entityManager->initRepository(ProductUserRating::class);
        $this->entityManager->initRepository(Provider::class);
        //$this->repoRating = $this->entityManager->getRepository(ProductUserRating::class);
    }

    /**
     * return Top brands
     *
     * url:  /ajax-get-brands-top
     * @return JSON
     */
    public function getBrandsTopAction()
    {
        $count_columns = new \Laminas\Db\Sql\Expression("count(`product_id`) as `count`, `product_id` as product_id");
        $productsTop = ProductHistory::findAll(['columns' => [$count_columns], 'group' => ['product_id'], 'having' => ['count > 1'], 'group' => ['product_id'], 'limit' => Resource::SQL_LIMIT_PRODUCTCARD_TOP,])->toArray();
        $productsId = ArrayHelper::extractId($productsTop);
        $productWhere = new Where();
        $productWhere->in("id", $productsId);
        $products = $this->productRepository->findAll(["where" => $productWhere, "columns" => ["brand_id"], "group" => "brand_id"])->toArray();
        $brandsId = ArrayHelper::extractId($products, "brand_id");
        $brands = Brand::findAll(["where" => ["id" => $brandsId], 'limit' => Resource::SQL_LIMIT_BRAND_SLIDER])->toArray();

        foreach ($brands as $brand) {
            $return[] = $brand;
        }

        return new JsonModel($return);
    }

    /**
     * url:  /ajax-get-basket-json
     * @return JsonModel
     */
    public function ajaxGetBasketJsonAction()
    {
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;

        if (empty($return['userId'])) {
            return $this->getResponse()->setStatusCode(403);
            //return;
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

    /**
     * url:  /ajax/del-from-basket
     * @return JsonModel
     */
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

    /**
     * url:  /ajax-get-category-filters
     * @return JsonModel
     */
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

    /**
     * url:  /ajax-get-order-list
     * @return JsonModel
     */
    public function getUserOrderListAction()
    {
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        $return["result"] = true;
        $orders = ClientOrder::findAll(["where" => ['user_id' => $userId], "order" => ['date_created desc']]);

        if ($orders->count() < 1) {
            return new JsonModel(["result" => false]);
        }
        $return["order_list"] = $this->htmlProvider->orderList($orders);

        return new JsonModel($return);
    }

    /**
     *
     * @param int $userId
     * @param string $orderId
     * @return array
     */
//    private function getBasketProductMap($userId, $orderId = 0)
//    {
//        $products = [];
//        $where = new Where();
//        $where->equalTo('user_id', $userId);
//
//        if ($orderId == 0) {
//            //$where->notEqualTo('order_id', $orderId);
//        } else {
//            $where->equalTo('order_id', $orderId);
//        }
//
//        $columns = ['product_id'];
//        $userBasketHistory = Basket::findAll(['where' => $where, 'columns' => $columns]);
//        $providers = [];
//        foreach ($userBasketHistory as $basketItem) {
//            $product_id = $basketItem->getProductId();
//            try {
//                $product = $this->handBookRelatedProductRepository->find(['id' => $product_id]);
//                $products[$product_id] = ["image" => $product->receiveProductImages()->current()->getHttpUrl(), "title" => $product->getTitle()];
//                $providers[] = $product->getProviderId();
//            } catch (\Throwable $ex) {
//                return ["result" => false, 'error' => $ex->getMessage()];
//            }
//        }
//
//        return empty($products) ? ["result" => false, "error" => "order $orderId have not basket products " . Json::encode(['where' => $where, 'columns' => $columns])] : ["result" => true, "products" => $products, "providers" => $providers];
//    }

    private function getProvidersMap($providersIdArray)
    {
        //$providerMap = [];
        if (!empty($providersIdArray)) {
            $providers = Provider::findAll(["where" => ["id" => $providersIdArray]]);
            foreach ($providers as $provider) {
                $providerMap[$provider->getId()] = ["image" => $provider->getImage(), "title" => $provider->getTitle()];
            }
        }

        return $providerMap ?? [];
    }

    /**
     * url:  /ajax-get-order-page
     * @return JsonModel
     */
    public function getUserOrderPageAction()
    {
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        $return["result"] = false;
        $post = $this->getRequest()->getPost();
        $return['order_id'] = $post->orderId;
        $order = ClientOrder::findAll(["where" => ['user_id' => $return['userId'], 'order_id' => $return['order_id']]]);
        $return["order_info"] = $this->htmlProvider->orderList($order);
        $providers = $this->getClientOrderProviders($return["order_info"][0]["deliveryInfo"]["delivery_info"]["deliveries"]);
        $return["providersMap"] = $this->getProvidersMap($providers);
        $return["result"] = true;

        return new JsonModel($return);
    }

    private function getClientOrderProviders($deliveryes)
    {
        foreach ($deliveryes as $delivery) {
            $requsitions[] = $delivery["requisitions"];
        }

        if (empty($requsitions)) {
            return [];
        }

        foreach ($requsitions as $requsition) {

            foreach ($requsition as $req) {
                $providers[] = $req['provider_id'];
            }
        }

        return $providers ?? [];
    }

    /**
     * url:  /ajax-check-order-status
     * @return JsonModel
     */
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

    /**
     * url:  /user-delete-address
     * @return JsonModel
     */
    public function ajaxUserDeleteAddressAction()
    {
        $post = $this->getRequest()->getPost();
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        $user = User::find(['id' => $userId]);

        if (empty($user) || empty($user->getPhone())) {
            $this->getResponse()->setStatusCode(403);
            return;
        }

        $return['reload'] = $post->reload;
        $return['dataId'] = $post->dataId;
        $remove = UserData::remove(['where' => ['user_id' => $userId, 'id' => $return['dataId']]]);
        $return['removeresult'] = $remove->count();

        return new JsonModel($return);
    }

    /**
     * url:  /user-set-default-address
     * @return JsonModel
     */
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

        if (empty($userData)) {
            $return['error'] = "adress not found";
            return new JsonModel($return);
        }

        $userGeoData = $userData->getGeodata();
        $userData->setTime(time());
        $userData->persist(['user_id' => $userId, 'id' => $return['dataId']]);
        $return['updatelegalstore'] = $this->commonHelperFuncions->updateLegalStores($userGeoData);

        if (empty($return['updatelegalstore']["result"])) {
            return $this->getResponse()->setStatusCode(401);
        }

        return new JsonModel($return);
    }

    /**
     * url:  /ajax-basket-changed
     * @return JsonModel
     */
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

    /**
     * url:  /ajax-basket-check-before-send
     * @return JsonModel
     */
    public function basketCheckBeforeSendAction()
    {
        $userId = $this->identity();
        $user = User::find(['id' => $userId]);

        if (empty($user) || empty($user->getPhone())) {
            return new JsonModel(["result" => false, "reload" => true, "reloadUrl" => "/",]);
        }

        $userData = UserData::findAll(['where' => ['user_id' => $userId], 'order' => 'timestamp DESC'])->current();

        if (empty($userData)) {
            return new JsonModel(["result" => false, "reload" => true, "reloadUrl" => "/"]);
        }
        $return['updatelegalstore'] = $this->commonHelperFuncions->updateLegalStores($userData->getGeodata());
        $post = $this->getRequest()->getPost();
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

    /**
     * url:  /ajax/add-to-favorites
     * @return JsonModel
     */
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

    /**
     * url:  /ajax/remove-from-favorites
     * @return JsonModel
     */
    public function removeFromFavoritesAction()
    {
        if (!$userId = $this->identity()) {

            return $this->getResponse()->setStatusCode(403);
        }
        if (empty($productId = $this->getRequest()->getPost()->productId)) {

            return new JsonModel(['result' => false, "description" => " product undefinded "]);
        }
        ProductFavorites::remove(['where' => ['user_id' => $userId, 'product_id' => $productId]]);

        return new JsonModel(['result' => true, "description" => "product $productId removed from favorites", 'lable' => Resource::ADD_TO_FAVORITES]);
    }

    /*
     *  ?????????????????????????? ???????????? ?????? http://api4.searchbooster.io
     * @return json
     */

    public function searchBoosterApiAction()
    {
        $json = file_get_contents(str_replace("/get-search-booster-api", "", "http://api4.searchbooster.io" . $_SERVER["REQUEST_URI"]));
        $return = (!empty($json)) ? Json::decode($json, Json::TYPE_ARRAY) : [];

        return new JsonModel($return);
    }

    /**
     * /ajax/add-to-basket
     * @return JsonModel
     */
    public function addToBasketAction()
    {
        if (!$userId = $this->identity()) {

            return $this->getResponse()->setStatusCode(403);
        }
        //$return = ["error" => true, "count" => 0];
        $return['total'] = $return['count'] = 0;
        $post = $this->getRequest()->getPost();
        if (!empty($productId = $post->product)) {
            //$return['error'] = false;
            $basketItem = Basket::findFirstOrDefault(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);
            $basketItemTotal = (int) $basketItem->getTotal();
            $basketItem->setUserId($userId);
            $basketItem->setProductId($productId);
            $productadd = $this->handBookRelatedProductRepository->findAll(['where' => ['id' => $productId]])->current();
            $productaddPrice = (int) $productadd->getPrice();
            $productaddDiscount = (int) $productadd->getDiscount();
            $productaddPrice = MathHelper::roundRealPrice($productaddPrice, $productaddDiscount); //$productaddPrice - $productaddPrice * $productaddDiscount / 100;
            $basketItem->setDiscount($productaddDiscount);
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

            if (!empty($pId = $b->productId) and!empty($product = $this->productRepository->find(['id' => $pId]))) {
                $return['products'][] = [
                    "id" => $pId,
                    "name" => $product->getTitle(),
                    "url" => $product->getUrl(),
                    "count" => $b->total,
                    'image' => $this->productImageRepository->findFirstOrDefault(["product_id" => $pId])->getHttpUrl(),
                ];
                $return['total'] += $b->total;
                $return['count']++;
            }
        }

        return new JsonModel($return);
    }

    /**
     * url:  /ajax/calculate-basket-item
     * @return JsonModel
     */
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
        if (null == $product or!$productPrice = (int) $product->getPrice() || !$productCount = (int) $post->count or $productCount < 1) {
            return new JsonModel(["error" => true, "errorMessage" => "product price error"]);
        }
        $basketItem = Basket::findFirstOrDefault(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);
        $basketItem->setTotal($productCount);
        $basketItem->persist(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);
        $productPrice = MathHelper::roundRealPrice($productPrice, $product->getDiscount()); 
        $return['totalNum'] = (int) $productPrice * $productCount;
        $return['totalFomated'] = number_format($return['totalNum'] / 100, 0, ',', '&nbsp;');
        return new JsonModel($return);
    }

    /**
     * url:  /ajax-basket-order-merge
     * @return ViewModel
     */
    public function basketOrderMergeAction()
    {
        $param = (!empty($delivery_params = Setting::find(['id' => 'delivery_params']))) ? Json::decode($delivery_params->getValue(), Json::TYPE_ARRAY) : [];
        if (!$userId = $this->identity()) {
            return $this->getResponse()->setStatusCode(403);
        }
        $post = $this->getRequest()->getPost();
        $timepoint = $post->timepoint ?? [0, 0];
        //$selectedtimepoint = [];
        $selectedtimepoint[0][$timepoint[0]] = " checked ";
        $selectedtimepoint[1][$timepoint[1]] = " checked ";

        $return = $this->htmlProvider->basketMergeData($post, $param);
        $return['ordermerge'] = $post->ordermerge;
        $return['selectedtimepoint'] = $selectedtimepoint;
        $return['timepoint'] = $timepoint;
        $return['timepointtext1'] = $post->timepointtext1;
        $return['timepointtext3'] = $post->timepointtext3;
        $view = new ViewModel($return);
        $view->setTemplate('application/common/basket-order-merge');

        return $view->setTerminal(true);
    }

    /**
     * url:  /ajax-basket-pay-card-info
     * @return ViewModel
     */
    public function basketPayCardInfoAction()
    {
        if (!$userId = $this->identity()) {
            return $this->getResponse()->setStatusCode(403);
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

    /**
     * url:  /ajax-basket-pay-info
     * @return ViewModel
     */
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
        $row['payEnable'] = ($row['producttotal'] > 0 and ($row['countSelfdelevery'] || ($row['countDelevery'] /* and $timeDelevery */))) ? true : false;
        $cardInfo = '';

        if ($post->cardinfo) {
            $userPaycards = UserPaycard::findAll(["where" => ["user_id" => $userId, "card_id" => $post->cardinfo]]);
            $cardUpdate = $userPaycards->current();
            $cardInfo = $cardUpdate->getPan();
            $cardUpdate->setTime(time())->persist(["user_id" => $userId, "card_id" => $post->cardinfo]);
        }

        $row['basketUser'] = $basketUser;
        $row['ordermerge'] = $post->ordermerge;
        //$row['priceDelevery'] = $param['hourPrice'];
        $row['priceDeleveryMerge'] = $param['mergePrice'];
        $row['priceDeleveryMergeFirst'] = $param['mergePriceFirst'];
        $row['addressDelevery'] = StringHelper::cutAddressCity($basketUser['address']);
        $row['priceSelfdelevery'] = 0;
        $row["cardinfo"] = $cardInfo;
        $row['paycard'] = $post->paycard;
        $row['timeDelevery'] = $timeDelevery;
        $view = new ViewModel($row);

        return $view->setTemplate('application/common/basket-payinfo')->setTerminal(true);
    }

    /**
     *
     * @return ViewModel
     */
//    public function previewAction()
//    {
//        $this->layout()->setTemplate('layout/preview');
//
//        return new ViewModel(['menu' => '',]);
//    }

    /**
     * url:  /ajax-get-legal-store
     * @return JsonModel
     */
    public function ajaxGetLegalStoreAction()
    {
        //$post = $;
        if (!$json = $this->getRequest()->getPost()->value) {
            return new JsonModel(null);
        }

        try {
            $decoded = Json::decode($json);
        } catch (LaminasJsonRuntimeException $e) {
            return new JsonModel(["result" => false, "error" => $e->getMessage()]);
        }
        //$decoded->data;
        if (!$decoded->data->house) {
            return new JsonModel(["result" => false, "error" => Resource::USER_ADDREES_ERROR_MESSAGE . ""]);
        }

        $return = $this->commonHelperFuncions->updateLegalStores($json);

        if (empty($return["result"])) {
            return $this->getResponse()->setStatusCode(504);
        }


        return new JsonModel($return);
    }

    /**
     * url:  /ajax-add-user-address
     * @return JsonModel
     */
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

    /**
     * url:  /ajax-set-user-address
     * @return JsonModel
     */
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

    /**
     * disabled.
     * rating values provide 1c now
     *
     * @return  JsonModel
     */
    public function setProductRatingAction()
    {
        return new JsonModel(["result" => false, "description" => "Service unavailble"]);

//        if (empty($param['user_id']  = $this->identity()) or empty($param['product_id'] = $this->getRequest()->getPost()->productId)) {
//            return $this->getResponse()->setStatusCode(403);
//        }
//
//        $patternRating = Resource::PRODUCT_RATING_VALUES;
//        $rating =  $this->getRequest()->getPost()->rating;
//        $param['rating'] = $patternRating[$rating]) ?? end($patternRating);
//
//        return new JsonModel($this->productRepository->setProductRating($param));
    }

}
