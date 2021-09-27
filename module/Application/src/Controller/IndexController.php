<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
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
//use Application\Model\RepositoryInterface\StoreRepositoryInterface;
use Application\Model\RepositoryInterface\BasketRepositoryInterface;
use Application\Model\RepositoryInterface\ColorRepositoryInterface;
use Application\Model\RepositoryInterface\SettingRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
//use Application\Service\CommonHelperFunctionsService;
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
use Application\Model\Entity\Brand;
use Application\Model\Entity\Store;
use Application\Model\Entity\StockBalance;
//use Application\Model\Entity\Category;
//use Application\Model\Entity\ProductHistory;
use Laminas\Json\Json;
use Application\Service\HtmlProviderService;
use Application\Service\HtmlFormProviderService;
use Application\Resource\Resource;
use Laminas\Session\Container; // as SessionContainer;
use Laminas\Session\SessionManager;
use Application\Adapter\Auth\UserAuthAdapter;
use Laminas\Db\Sql\Where;
use Application\Model\Entity\User;
use Application\Model\Entity\UserData;
use Application\Helper\ArrayHelper;
use Application\Helper\StringHelper;
use Application\Model\Entity\ProductFavorites;
use Application\Model\Entity\ProductHistory;
//use Application\Model\Entity\ProductHistory;

class IndexController extends AbstractActionController
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
    
    /**
    * @var CommonHelperFunctions
    */
    private $commonHelperFuncions;
    
    private $entityManager;
    private $config;
    private $htmlProvider;
    private $htmlFormProvider;
    private $userRepository;
    private $authService;
    private $productCharacteristicRepository;
    private $colorRepository;
    private $basketRepository;
    private $productHistoryRepository;
    //private $sessionContainer;
    private $sessionManager;


    public function __construct(TestRepositoryInterface $testRepository, CategoryRepositoryInterface $categoryRepository,
            ProviderRepositoryInterface $providerRepository, StoreRepositoryInterface $storeRepository,
            ProductRepositoryInterface $productRepository, FilteredProductRepositoryInterface $filteredProductRepository,
            BrandRepositoryInterface $brandRepository, ColorRepositoryInterface $colorRepository, SettingRepositoryInterface $settingRepository,
            CharacteristicRepositoryInterface $characteristicRepository,
            PriceRepositoryInterface $priceRepository, StockBalanceRepositoryInterface $stockBalanceRepository,
            HandbookRelatedProductRepositoryInterface $handBookProduct,  $commonHelperFunctions,  
            $entityManager, $config,   HtmlProviderService $htmlProvider, HtmlFormProviderService $htmlFormProvider, UserRepository $userRepository, AuthenticationService $authService,
            ProductCharacteristicRepositoryInterface $productCharacteristicRepository, BasketRepositoryInterface $basketRepository/* , $sessionContainer */, $sessionManager)
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
        $this->commonHelperFuncions = $commonHelperFunctions;
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->htmlProvider = $htmlProvider;
        $this->htmlFormProvider = $htmlFormProvider;
        $this->userRepository = $userRepository;
        $this->authService = $authService;
        $this->productCharacteristicRepository = $productCharacteristicRepository;
        $this->basketRepository = $basketRepository;
//        $this->sessionContainer = $sessionContainer;
        $this->sessionManager = $sessionManager;

        $this->entityManager->initRepository(ClientOrder::class);
        $this->entityManager->initRepository(Setting::class);
        $this->entityManager->initRepository(Delivery::class);
        $this->entityManager->initRepository(UserPaycard::class);
        $this->entityManager->initRepository(ProductHistory::class);
        $this->entityManager->initRepository(ProductFavorites::class);
        $this->entityManager->initRepository(Brand::class);
        $this->entityManager->initRepository(Store::class);
        $this->entityManager->initRepository(StockBalance::class);
        //$this->entityManager->initRepository(Category::class);
    }

    public function onDispatch(MvcEvent $e)
    {
        $userAuthAdapter = new UserAuthAdapter(/* $this->userRepository *//* $this->sessionContainer */);
        $result = $this->authService->authenticate($userAuthAdapter);
        $code = $result->getCode();
        if ($code != \Application\Adapter\Auth\UserAuthResult::SUCCESS) {
            throw new \Exception('Unknown error in IndexController');
        }
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);
        $userId = $this->identity();
        $user = $this->userRepository->find(['id' => $userId]);
        //$userAddressHtml = $this->htmlProvider->writeUserAddress($user);
        $userAddressArray = $this->htmlProvider->getUserAddresses($user, Resource::LIMIT_USER_ADDRESS_LIST);
        $userInfo = $this->commonHelperFuncions->getUserInfo($user);
        $mainMenu = (!empty($mainMenu = Setting::find(['id' => 'main_menu']))) ? $mainMenu = $this->htmlProvider->getMainMenu($mainMenu) : [];
        $addressLegal = ($userInfo["userAddress"]) ? true : false;
        $userLegal = ($userInfo["userid"] and $userInfo["phone"]) ? true : false;

        // Return the response
        $this->layout()->setVariables([
            //'headerText' => $this->htmlProvider->testHtml(),
            //'footerText' => 'banzaii',
            //'catalogCategoties' => $this->categoryRepository->findAllCategories("", 0, $this->params()->fromRoute('id', '')),
            'categoryTree' => $this->categoryFilteredTree(),
          //  'userAddressHtml' => $userAddressHtml,
            'addressLegal' => $addressLegal,
            'addresses' =>  $userAddressArray,
            'addressesJson' => json_encode($userAddressArray, JSON_UNESCAPED_UNICODE),
            'userLegal' => $userLegal,
            'userinfo' => $userInfo,
            //'username' => $userInfo['name'],
            //'userphone' => $userInfo['phone'],
            'mainMenu' => $mainMenu,
            'basketProductsCount' => $this->commonHelperFuncions->basketProductsCount($userId),
        ]);
        return $response;
    }

    public function myLoginAction()
    {
        $this->layout()->setTemplate('layout/my-layout');
        return new ViewModel([]);
    }

    public function signupAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        $container = new Container();
        $password = $post['password'];
        if('123451' == $password) {
            $container->signedUp = true;
            return $this->redirect()->toUrl('/');
        }
        $container->signedUp = false;
        return $this->redirect()->toUrl('/my-login');
    }

//    private function matchProduct(HandbookRelatedProduct $product, $characteristics)
//    {
//        $flags = [];
//        foreach ($characteristics as $key => $value) {
//            $found = $this->productCharacteristicRepository->find(['characteristic_id' => $key, 'product_id' => $product->getId()]);
//            if (null == $found) {
//                $flags[$key] = false;
//                continue;
//            }
//            $type = $found->getType();
//            switch ($type) {
//                case CharacteristicRepository::INTEGER_TYPE:
//                    list($left, $right) = explode(';', $value[0]);
//                    $flags[$key] = !($found->getValue() < $left || $found->getValue() > $right);
//                    break;
//                case CharacteristicRepository::BOOL_TYPE:
//                    $flags[$key] = ($found->getValue() == $value);
//                    break;
//                default:
//                    $flags[$key] = in_array($found->getValue(), $value);
//                    break;
//            }
//        }
//        foreach ($flags as $f) {
//            if (!$f) {
//                return false;
//            }
//        }
//        return true;
//    }
//
//    private function getProducts($params)
//    {
//        $where = new \Laminas\Db\Sql\Where();
//        list($low, $high) = explode(';', $params['priceRange']);
//        $where->lessThanOrEqualTo('price', $high)->greaterThanOrEqualTo('price', $low);
//        $where->equalTo('category_id', $params['category_id']);
//        //$where->in('category_id', $params['category_id']);
//
//        unset($params['offset']);
//        unset($params['limit']);
//        $params['where'] = $where;
//
//        $products = $this->handBookRelatedProductRepository->findAll($params);
//        $filteredProducts = [];
//        foreach ($products as $product) {
//            $matchResult = $this->matchProduct($product, $params['characteristics']);
//            if ($matchResult) {
//                $filteredProducts[] = $product;
//            }
//        }
//        return $filteredProducts;
//    }

    public function clientOrdersAction()
    {
        $userId = $this->identity();
        $user = User::find(['id' => $userId]);
        //$userData = $user->getUserData();
        $userPhone = $user->getPhone();
        if (!$userPhone) {
            $this->getResponse()->setStatusCode(403);
            $vw = new ViewModel();
            $vw->setTemplate('error/403.phtml');
            return $vw;
        }

        return new ViewModel([
            'title' => Resource::ORDER_TITLE, //  $container->item
            //'orders'=> $orderList,
            "auth" => $userPhone,
        ]);
    }
    
    
    public function clientOrderPageAction()
    {
        $userId = $this->identity();
        $user = User::find(['id' => $userId]);
        $userPhone = $user->getPhone();
        if (!$userPhone) {
            return $this->unauthorizedLocation();
        }
        if (empty($orderId = $this->params()->fromRoute('id', '')) or null ==  $order = ClientOrder::find(['user_id' => $userId, 'order_id' => $orderId ])){
            $this->getResponse()->setStatusCode(301);
            return $this->redirect()->toRoute('/user/orders');
            
        }
        $orderInfo = $this->htmlProvider->orderList([$order]);
        
        return new ViewModel([
            'title' => "Заказ №".$orderInfo[0]['orderId']. "",//. Resource::ORDER_TITLE, //  $container->item
            'orderDate' => strftime('%c', (int)$orderInfo[0]['orderDate']),
            //'orders'=> $orderList,
            "orderInfo" => $this->htmlProvider->orderList([$order])[0],
        ]);
        
        
    }    
        
    
    
    
    

    public function indexAction()
    {
        $container = new Container();
        if($container->signedUp != true) {
            return $this->redirect()->toUrl('/my-login');
        }

//        $userPaycard = new UserPaycard();
//        $userPaycard->setUserId('000001');
//        $userPaycard->setCardId('000000001');
//        $userPaycard->setPan('pan-hujpan');
//        $userPaycard->setTime(time());
//        $userPaycard->persist(['card_id'=>'000000001']);
//
//        $up = UserPaycard::find(['card_id' => '000000001']);
//
//        print_r($up);


//        $container = new Container();
//        print_r($container->banzaii);
//        exit;
//        if(true) {
//            $this->redirect()->toUrl('/login');
//        }

//        $product = $this->handBookRelatedProductRepository->find(['id' => '000000000001']);
//        $provider = $product->getProvider();
//        $stores = $provider->getStoreArray();
        //$s = $provider->storesToArray();

//        $tree = $this->categoryRepository->categoryTree("", 0, $this->params()->fromRoute('id', ''));
//        echo '<pre>';
//        print_r($tree);
//        echo '</pre>';
//        exit;
//        $user = User::findFirstOrDefault(['id' => 497]);
//        $userData = new UserData();
//        $userData->setUserId($user->getId());
//        $userData->setAddress('address5555');
////        $userData->setFiasLevel(8);
////        $userData->setFiasId('asdfasdf');
//        $userData->setGeodata('{"data":{"fias_id": "22222222", "fias_level": "8"}}');
//        $userData->setTime(time());
//        $user->setUserData([$userData]);
//        $user->persist(['id' => $user->getId()]);
//        $delivery = new Delivery();
//        $delivery->setId(null);
//        $delivery->setDeliveryId('0000002');
//        $delivery->setOrderId('0000111');
//        $delivery->setDateCreated(time());
//        $delivery->persist(['id' => $delivery->getId()]);
        // $clientOrder = new ClientOrder();
//        $clientOrder = ClientOrder::findFirstOrDefault(['id' => null]);
//        $clientOrder->setId(null);
//        $clientOrder->setOrderId('00000000003');
//        $clientOrder->setDateCreated(time());
////        $date = (new \DateTime("now"))->format('Y-m-d h:i:s');
////        $clientOrder->setTimestamp($date);
//
//        $clientOrder->persist(['id' => $clientOrder->getId()]);
//        $validator = new \Laminas\Validator\EmailAddress();
//
//        $email = 'alex.kraskov@gmail.com';
//
//        if ($validator->isValid($email)) {
//            // email appears to be valid
//            print_r('ok');
//            exit;
//        } else {
//            // email is invalid; print the reasons
//            foreach ($validator->getMessages() as $message) {
//                echo "$message\n";
//            }
//            exit;
//        }
//        $validator = new \Laminas\Validator\Regex(['pattern' => '/^Test/']);
//
//        $validator->isValid("Test"); // returns true
//        $validator->isValid("Testing"); // returns true
//        $validator->isValid("Pest"); // returns false
        //$container = $this->sessionContainer;// new Container(Resource::SESSION_NAMESPACE);
//        $container = new Container(Resource::SESSION_NAMESPACE);
//        if(isset($container->item)) {
//            print_r($container->item);
//        }else{
//            print_r('null');
//        }
//        exit;
        return new ViewModel([
                //'fooItem' => 'banzaii', //  $container->item
        ]);
    }

    public function basketAction()
    {
        $basketUser['id'] = $userId = $this->identity();
        $user = $this->userRepository->find(['id' => $userId]);
        $basketUser['userId'] = $user->getUserId();
        $basketUser['phone'] = $user->getPhone();
        $basketUser['phoneformated'] = StringHelper::phoneFromNum($basketUser['phone']);
        $basketUser['name'] = $user->getName();

        $userData = $user->getUserData();
        //$count = $userData->count();
        if (!empty($userData) and $userData->count()) {
            $basketUser['address'] = $userData->current()->getAddress();
            $basketUser['geodata'] = $userData->current()->getGeoData();
        }
        if (!$basketUser['phone'] or!$basketUser['name']) {
            $legalUser = false;
        } else {
            $legalUser = true;
        }
        $where = new Where();
        $where->equalTo('user_id', $userId);
        $where->equalTo('order_id', 0);
        $columns = ['product_id', 'order_id', 'total', 'price'];
        $basket = $this->basketRepository->findAll(['where' => $where, 'columns' => $columns]);

        $content = $this->htmlProvider->basketData($basket, $userId);
       
        
        return new ViewModel([
            /* "providers" => $providers, */
            "content" => $content["product"],
            "title" => "Корзина",
            "titleH" => $content["title"],
            "basketUser" => $basketUser,
       //     "cardinfo" => $cardInfo,
            "countproviders" => $content["countproviders"],
            "countprducts" => $content["countproducts"],
            "legalUser" => $legalUser,
            // "legalAddress" => $legalAddress,
            'textdefault' => \Application\Resource\Resource::BASKET_SAYCHAS_do . ", ",
            "register_title" => Resource::MESSAGE_ENTER_OR_REGISTER_TITLE,
            "register_text" => Resource::MESSAGE_ENTER_OR_REGISTER_TEXT,
        ]);
    }

    public function previewAction()
    {
        $container = new Container();
        if($container->signedUp != true) {
            return $this->redirect()->toUrl('/my-login');
        }

        return new ViewModel([
            'menu' => null,
        ]);
    }
    
    public function clientFavoritesPageAction()
    {
        $container = new Container();
        if($container->signedUp != true) {
            return $this->redirect()->toUrl('/my-login');
        }

       $userId = $this->identity();
        $user = User::find(['id' => $userId]);
        $userInfo = $this->commonHelperFuncions->getUserInfo($user);
        if (empty($userInfo["phone"])) {
            return $this->unauthorizedLocation();
        }
        
        return new ViewModel([
            'userInfo' => $userInfo,
            
        ]);
    }
    
    
    

    public function productPageAction()
    {
        $container = new Container();
        if($container->signedUp != true) {
            return $this->redirect()->toUrl('/my-login');
        }
        $userId = $this->identity();
        $product_id = $this->params()->fromRoute('id', '');
        $params['equal'] = $product_id;
        if (empty($product_id) or empty($products = $this->productRepository->filterProductsByStores($params))) {
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            $view = new ViewModel();
            return $view->setTemplate('error/404.phtml');
        }
        $productPage = $this->htmlProvider->productPageService($products);
        //$categoryId = $productPage['categoryId'];
        $breadCrumbs = [];
        if (!empty($matherCategories = $this->categoryRepository->findAllMatherCategories($productPage['categoryId']))) {
            $breadCrumbs = array_reverse($matherCategories);
        }
        $productPage['breadCrumbs'] = $breadCrumbs;
        $productPage['isFav'] = $this->commonHelperFuncions->isInFavorites($product_id, $userId );
        $this->addProductToHistory($product_id);
        //$bread = $this->htmlProvider->breadCrumbs($breadSource);
        $productPage['category'] = $this->categoryRepository->findCategory(['id' => $productPage['categoryId']])->getTitle();
        $productPage['id'] = $product_id;
       
//        
//        $vwm = [
//            'id' => $product_id,
//            'title' => $productPage['title'],
//            'images' => $productPage['images'],
//            'category' => $productPage['category'],
//            'characteristics' => $productPage["characteristics"],
//            'product' => $productPage['card'],
//            'description' => $productPage['description'],
//            'append' => $productPage['appendParams'],
//            'isFav' => $this->commonHelperFuncions->isInFavorites($product_id, $userId ),
//            'price' => $productPage['price'],
//            'provider' => $productPage['provider'],
//            'brand' => $productPage['brand'],
//            'price_formated' => $productPage['price_formated'],
//            'breadCrumbs' => $breadCrumbs,
//        ];
        return new ViewModel($productPage);
    }

    public function catalogAction()
    {
        $container = new Container();
        if($container->signedUp != true) {
            return $this->redirect()->toUrl('/my-login');
        }
        $category_id = $this->params()->fromRoute('id', '');
       
        if (empty($category_id) or empty($categoryTitle = $this->categoryRepository->findCategory(['id' => $category_id])->getTitle())) {
            $this->getResponse()->setStatusCode(301);
            return $this->redirect()->toRoute('home');
        }
        //$categories = $this->categoryRepository->findAllCategories("", 0, $category_id);
        $matherCategories = $this->categoryRepository->findAllMatherCategories($category_id);
        if (!empty($matherCategories = $this->categoryRepository->findAllMatherCategories($category_id))) {
            $breadCrumbs = array_reverse($matherCategories);
        } else {
            $breadCrumbs = [];
        }
        //$categoryTree = $this->categoryRepository->findCategoryTree($category_id, [$category_id]);
        //$minMax = $this->handBookRelatedProductRepository->findMinMaxPriceValueByCategory($categoryTree);
        //$filters = $this->productCharacteristicRepository->getCategoryFilter($matherCategories);
        //$filterForm = $this->htmlProvider->getCategoryFilterHtml($filters, $category_id, $minMax);
        return new ViewModel([ "catalog" => [],"title" => $categoryTitle,"id" => $category_id,"breadCrumbs" => $breadCrumbs, /*'filterform' => $filterForm,*/
        ]);
    }

    public function categoryAction($category_id = false)
    {
        if (empty($category_id)) {
            $category_id = $this->params()->fromRoute('id', '');
        }
        $categories = (!empty($params = Setting::find(['id' => 'main_menu']))) ? Json::decode($params->getValue(), Json::TYPE_ARRAY) : [];
        $category = $categories[$category_id];
        return new ViewModel(["title" => $category["title"],]);
    }
    
    public function brandProductsAction()
    {
        $brand_id = $this->params()->fromRoute('brand_id', '');
        $category_id = $this->params()->fromRoute('category_id', '');
        if (empty($brand = Brand::find(["id"=> $brand_id ]))){
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            $view = new ViewModel();
            return $view->setTemplate('error/404.phtml');
        }
        $brandTitle = $brand->getTitle();
        $categories = $this->getBrandCategories($brand_id); 
           $categoryTitle = Resource::THE_ALL_PRODUCTS; 
        $breadCrumbs[]=[null, $brandTitle];
        foreach ($categories as $category) {
            if ($category->getId() == $category_id) {
                $categoryTitle =  $category->getTitle();
            }
            $breadCrumbs[] = [$category->getId(), $category->getTitle()];
        }
        return new ViewModel(['breadCrumbs' => $breadCrumbs,'logo' => $brand->getImage() , 'id' => $brand_id,'category_id' => $category_id,"title" =>  $brandTitle, 'category_title' => $categoryTitle,]);
    }
    
    public function providerProductsAction()
    {
        $provider_id = $this->params()->fromRoute('provider_id', '');
        $category_id = $this->params()->fromRoute('category_id', '');
        if (empty($provider = Provider::find(["id"=> $provider_id ]))){
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            $view = new ViewModel();
            return $view->setTemplate('error/404.phtml');
        }
        $providerTitle = $provider->getTitle();
        $categories = $this->getProviderCategories($provider_id); 
           $categoryTitle = Resource::THE_ALL_PRODUCTS; 
        $breadCrumbs[]=[null, $providerTitle];
        foreach ($categories as $category) {
            if ($category->getId() == $category_id) {
                $categoryTitle =  $category->getTitle();
            }
            $breadCrumbs[] = [$category->getId(), $category->getTitle()];
        }
        return new ViewModel(['breadCrumbs' => $breadCrumbs, 'logo' => $provider->getImage() , 'id' => $provider_id,'category_id' => $category_id,"title" => $providerTitle, 'category_title' => $categoryTitle,]);
    }
    
    public function storeProductsAction()
    {        
        $store_id = $this->params()->fromRoute('store_id', '');
        $category_id = $this->params()->fromRoute('category_id', '');
        if (empty($store = Store::find(["id"=> $store_id ]))){
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            $view = new ViewModel();
            return $view->setTemplate('error/404.phtml');
        }
        $storeTitle = $store->getTitle();
        $categories = $this->getStoreCategories($store_id); //$this->getBrandCategories($brand_id);
        $categoryTitle = Resource::THE_ALL_PRODUCTS; 
        $breadCrumbs[]=[null, $storeTitle];
        foreach ($categories as $category) {
            if ($category->getId() == $category_id) {
                $categoryTitle =  $category->getTitle();
            }
            $breadCrumbs[] = [$category->getId(), $category->getTitle()];
        }
        return new ViewModel( ['breadCrumbs' => $breadCrumbs,'address' => StringHelper::cutAddress($store->getAddress()),'id' => $store_id,'category_id' => $category_id,"title" => $storeTitle, 'category_title' => $categoryTitle,]);
    }
    
    
    private function getStoreCategories($store_id)
    {
        $storeProducts = StockBalance::findAll([ "where" => ['store_id' => $store_id], 'columns' => ['product_id'], "group" => "product_id"])->toArray();
        $products = ArrayHelper::extractProdictsId($storeProducts);
        $storeProductsCategories = $this->productRepository->findAll(["where" => ["id" => $products], 'columns' => ["category_id"], 'group' => ["category_id"]]);
        foreach ($storeProductsCategories as $category){
            $categoriesArray[] = $category->getCategoryId();
        }
       return $this->categoryRepository->findAll(["where" => ["id" => $categoriesArray]]);//->toArray();
            
    }
    
    
    private function getBrandCategories($brand_id)
    {
        $brandProductsCategories = $this->productRepository->findAll(["where" => ["brand_id" => $brand_id], 'columns' => ["category_id"], 'group' => ["category_id"]]);
        foreach ($brandProductsCategories as $category){
            $categoriesArray[] = $category->getCategoryId();
        }
        return  $this->categoryRepository->findAll(["where" => ["id" => $categoriesArray]]);//->toArray();
    }
    
    private function getProviderCategories($provider_id)
    {
        $brandProductsCategories = $this->productRepository->findAll(["where" => ["provider_id" => $provider_id], 'columns' => ["category_id"], 'group' => ["category_id"]]);
        foreach ($brandProductsCategories as $category){
            $categoriesArray[] = $category->getCategoryId();
        }
        return  $this->categoryRepository->findAll(["where" => ["id" => $categoriesArray]]);//->toArray();
    }
    
    
    
    

    public function userAction($category_id = false)
    {
        $container = new Container();
        if($container->signedUp != true) {
            return $this->redirect()->toUrl('/my-login');
        }

        $userId = $this->identity(); //authService->getIdentity();//
        $user = User::find(['id' => $userId]);
        //$userData = 
        $phone = $user->getPhone();
        $userPaycards = UserPaycard::findAll(['where' => ["user_id" => $userId], "order" => "timestamp desc"]);
        $paycards =($userPaycards->count())?$userPaycards:null;
        $cardInfo = $this->htmlProvider->getUserPayCardInfoService($paycards);
        
        if (!$phone) {
            
            return $this->unauthorizedLocation();
        }
        $userPhone = StringHelper::phoneFromNum($phone);
        $title = ($user->getName()) ? $user->getName() : "Войти на сайт";
        return new ViewModel([
            "user" => $user,
            "userData" => $user->getUserData(),
            "userPhone" => $userPhone,
            "title" => $title, //."/$category_id",
            "id" => "userid: " . $userId,
            "bread" => "bread ",
            "auth" => ($user->getPhone()),
            "paycards" =>  $cardInfo, 
        ]);
    }
    
    private function packParams($params)
    {
        $a = [];
        foreach ($params['filter'] as $p) {
            $a[] = "find_in_set('$p', param_value_list)";
        }
        $res = ' 1';
        if (count($a) > 0) {
            $res = '(' . implode(' OR ', $a) . ')';
        }
        return $res;
    }
            
    private function unauthorizedLocation()
    {
           $this->getResponse()->setStatusCode(403);
           $vw = new ViewModel();
           $vw->setTemplate('error/403.phtml');
           return $vw;
    }
    
    private function addProductToHistory($productId)
    {
        $userId = $this->identity();
        //ProductHistory::remove(['user_id' => $userId, 'product_id' => $productId]);
        $historyItem = ProductHistory::findFirstOrDefault(['user_id' => $userId, 'product_id' => $productId]);
        $historyItem->setUserId($userId); 
        $historyItem->setProductId($productId); 
        $historyItem->setTime(time()); 
        $historyItem->persist(['user_id' => $userId, 'product_id' => $productId]);
    }
    
    private function isInFavorites ($productId, $userId)
    {
        if (!empty($userId)) {
            if (!empty(ProductFavorites::find(['user_id' => $userId, 'product_id' => $productId]))){
                return true;
            }
        }
        return  false; 
    }
    
    
}
