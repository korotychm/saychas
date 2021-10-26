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
//use Application\Model\Entity\ProductCharacteristic;
use Application\Model\RepositoryInterface\ProductCharacteristicRepositoryInterface;
use Application\Model\Repository\UserRepository;
//use Application\Model\Repository\CharacteristicRepository;
//use Application\Model\Entity\HandbookRelatedProduct;
use Application\Model\Entity\Provider;
use Application\Model\Entity\Setting;
use Application\Model\Entity\ClientOrder;
use Application\Model\Entity\Delivery;
use Application\Model\Entity\UserPaycard;
use Application\Model\Entity\Brand;
use Application\Model\Entity\Store;
use Application\Model\Entity\StockBalance;
use Application\Model\Entity\ProductImage;
//use Application\Model\Entity\Category;
//use Application\Model\Entity\ProductHistory;
use Laminas\Json\Json;
use Application\Service\HtmlProviderService;
use Application\Service\HtmlFormProviderService;
use Application\Resource\Resource;
use Laminas\Session\Container; // as SessionContainer;
//use Laminas\Session\SessionManager;
use Application\Adapter\Auth\UserAuthAdapter;
use Laminas\Db\Sql\Where;
use Application\Model\Entity\User;
//use Application\Model\Entity\UserData;
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
    //private $productHistoryRepository;
    //private $sessionContainer;
    private $sessionManager;

    public function __construct(TestRepositoryInterface $testRepository, CategoryRepositoryInterface $categoryRepository,
            ProviderRepositoryInterface $providerRepository, StoreRepositoryInterface $storeRepository,
            ProductRepositoryInterface $productRepository, FilteredProductRepositoryInterface $filteredProductRepository,
            BrandRepositoryInterface $brandRepository, ColorRepositoryInterface $colorRepository, SettingRepositoryInterface $settingRepository,
            CharacteristicRepositoryInterface $characteristicRepository,
            PriceRepositoryInterface $priceRepository, StockBalanceRepositoryInterface $stockBalanceRepository,
            HandbookRelatedProductRepositoryInterface $handBookProduct, $commonHelperFunctions,
            $entityManager, $config, HtmlProviderService $htmlProvider, HtmlFormProviderService $htmlFormProvider, UserRepository $userRepository, AuthenticationService $authService,
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
        $this->entityManager->initRepository(ProductImage::class);
    }

    public function onDispatch(MvcEvent $e)
    {
        /**/
        $container = new Container();
        
        if ($container->signedUp != true) {
            if (!in_array($this->getRequest()->getUri()->getPath(), ['/signup', '/my-login']) ){
                return $this->redirect()->toUrl('/my-login');
            }    
        }
        /**/
        $userAuthAdapter = new UserAuthAdapter();
        $result = $this->authService->authenticate($userAuthAdapter);
        $code = $result->getCode();
     
        if ($code != \Application\Adapter\Auth\UserAuthResult::SUCCESS) {
            throw new \Exception('Unknown error in IndexController');
        }
        
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
            'categoryTree' => $this->categoryRepository->categoryFilteredTree(),
            'addressLegal' => $addressLegal,
            'addresses' => $userAddressArray,
            'addressesJson' => json_encode($userAddressArray, JSON_UNESCAPED_UNICODE),
            'userLegal' => $userLegal,
            'userinfo' => $userInfo,
            'mainMenu' => $mainMenu,
            'basketProductsCount' => $this->commonHelperFuncions->basketProductsCount($userId),
        ]);
        
        return $response;
    }

     /**
     * @route /
     * @return ViewModel
     */
    public function indexAction()
    {
//        $container = new Container();
//        if ($container->signedUp != true) {
//            return $this->redirect()->toUrl('/my-login');
//        }
        return new ViewModel([
        ]);
    }
    
    /**
     * Show login page
     * 
     * @return ViewModel
     */
    public function myLoginAction()
    {
        $this->layout()->setTemplate('layout/my-layout');
        return new ViewModel([]);
    }

    /**
     * 
     * @return HTTP redirect
     */
    public function signupAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        $container = new Container();
        $password = $post['password'];
        if ('123451' == $password) {
            $container->signedUp = true;
            return $this->redirect()->toUrl('/');
        }
        $container->signedUp = false;
        return $this->redirect()->toUrl('/my-login');
    }

    /**
     * Client orders page
     * 
     * @route  /user/orders
     * @return ViewModel
     */
    public function clientOrdersAction()
    {
//        $container = new Container();
//        if ($container->signedUp != true) {
//            return $this->redirect()->toUrl('/my-login');
//        }
        $userId = $this->identity();
        $user = User::find(['id' => $userId]);
        $userPhone = (!empty($user)) ? $user->getPhone() : false ;
        
        if (!$userPhone) {
            return $this->unauthorizedLocation();
        }
        
        return new ViewModel([
            'title' => Resource::ORDER_TITLE, //  $container->item
            "auth" => $userPhone,
        ]);
    }

    /**
     * Client orders page
     * 
     * @route  /user/order[/:id]
     * @return ViewModel
     */
    public function clientOrderPageAction()
    {
//        $container = new Container();
//        if ($container->signedUp != true) {
//            return $this->redirect()->toUrl('/my-login');
//        }
        $userId = $this->identity();
        $user = User::find(['id' => $userId]);
        $userPhone = (!empty($user)) ? $user->getPhone() : false ;
        
        if (!$userPhone) {
            return $this->unauthorizedLocation();
        }
        
        if (empty($orderId = $this->params()->fromRoute('id', '')) or null == $order = ClientOrder::find(['user_id' => $userId, 'order_id' => $orderId])) {
            $this->getResponse()->setStatusCode(301);
            return $this->redirect()->toRoute('/user/orders');
        }
        
        $orderInfo = $this->htmlProvider->orderList([$order]);

        return new ViewModel(['title' => "Заказ №" . $orderInfo[0]['orderId'] . "", 'orderDate' => strftime('%c', (int) $orderInfo[0]['orderDate']), "orderInfo" => $this->htmlProvider->orderList([$order])[0],]);
    }

   /**
     * Basket page
     * 
     * @route  /basket
     * @return ViewModel
     */
    public function basketAction()
    {
//        $container = new Container();
//        if ($container->signedUp != true) {
//            return $this->redirect()->toUrl('/my-login');
//        }
        $userId = $this->identity();
        $where = new Where();
        $where->equalTo('user_id', $userId)->equalTo('order_id', 0);
        $columns = ['product_id', 'order_id', 'total', 'price'];
        $basket = $this->basketRepository->findAll(['where' => $where, 'columns' => $columns]);
        $content = $this->htmlProvider->basketData($basket, $userId);
        $content['title'] = Resource::THE_BASKET;
        $content["content"] = $content["product"];
        $user = $this->userRepository->find(['id' => $userId]);
        $content["basketUser"] = ['id' => $userId, 'userId' => $user->getUserId(), 'phone' => $user->getPhone(), 'phoneformated' => StringHelper::phoneFromNum($user->getPhone()), 'name' => $user->getName(),];
        $userData = $user->getUserData();
        //$count = $userData->count();
        
        if (!empty($userData) and $userData->count()) {
            $content["basketUser"]['address'] = $userData->current()->getAddress();
            $content["basketUser"]['geodata'] = $userData->current()->getGeoData();
        }
        
        $content["legalUser"] = (!$content["basketUser"]['phone'] or!$content["basketUser"]['name']) ? false : true;
        $content['textdefault'] = Resource::BASKET_SAYCHAS_do . ", ";
        $content["register_title"] = Resource::MESSAGE_ENTER_OR_REGISTER_TITLE;
        $content["register_text"] = Resource::MESSAGE_ENTER_OR_REGISTER_TEXT;

        return new ViewModel($content);
    }

    /**
     * Page for test
     * 
     * @route preview
     * @return ViewModel
     */
    public function previewAction()
    {
//        $container = new Container();
//        if ($container->signedUp != true) {
//            return $this->redirect()->toUrl('/my-login');
//        }

        return new ViewModel([
            'menu' => null,
        ]);
    }

    /**
     * 
     * @route  /user/favorites
     * @return ViewModel
     */
    public function clientFavoritesPageAction()
    {
//        $container = new Container();
//        if ($container->signedUp != true) {
//            return $this->redirect()->toUrl('/my-login');
//        }
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

    /**
     * Product page
     * 
     * @route /product[/:id]
     * @return ViewModel
     */
    public function productPageAction()
    {
//        $container = new Container();
//        if ($container->signedUp != true) {
//            return $this->redirect()->toUrl('/my-login');
//        }
        $userId = $this->identity();
        $product_id = $this->params()->fromRoute('id', '');
        $params['equal'] = $product_id;
        if (empty($product_id) or empty($products = $this->productRepository->filterProductsByStores($params)) or $products->count() < 1) {
            return $this->responseError404();
        }
        $param = (!empty($delivery_params = Setting::find(['id' => 'delivery_params']))) ? Json::decode($delivery_params->getValue(), Json::TYPE_ARRAY) : [];
        $productPage = $this->htmlProvider->productPageService($products);
        $productPage['breadCrumbs'] = ($productPage['categoryId'] and!empty($matherCategories = $this->categoryRepository->findAllMatherCategories($productPage['categoryId']))) ? array_reverse($matherCategories) : [];
        $productPage['isFav'] = $this->commonHelperFuncions->isInFavorites($product_id, $userId);
        $this->addProductToHistory($product_id);
        $productPage['category'] = (!empty($productPage['categoryId'])) ? $this->categoryRepository->findCategory(['id' => $productPage['categoryId']])->getTitle() : "";
        $productPage['id'] = $product_id;
        $productPage['images'] = $this->getProductImages($product_id); 
        $productPage['delivery_price'] = $param ['hourPrice'];
        
        return new ViewModel($productPage);
    }
    
    /**
     * Get  product images
     * 
     * @param string $product_id
     * @return array
     */
    private function getProductImages($product_id)
    {
        $images = ProductImage::findAll(["where" => ['product_id' => $product_id]])->toArray();
        
        foreach ($images as $image){
            $productImages[] = $image['http_url'];//->getHttpUrl();
        } 
      
        return $productImages ?? [];
    }

    /**
     * Category page
     * 
     * @route /catalog[/:id]
     * @return ViewModel
     */
    public function catalogAction()
    {
//        $container = new Container();
//        if ($container->signedUp != true) {
//            return $this->redirect()->toUrl('/my-login');
//        }

        $category_url = $this->params()->fromRoute('id', '');
        
        if (empty($category_url) or empty($category = $this->categoryRepository->findCategory(['url' => $category_url]))) {
            //exit (print_r(['url' => $category_url]));
            $this->getResponse()->setStatusCode(301);
            return $this->redirect()->toRoute('home');
        }
        
        $category_id = $category->getId();
        $breadCrumbs = ((!empty($matherCategories = $this->categoryRepository->findAllMatherCategories($category_id)))) ? array_reverse($matherCategories) : [];
        $categoryTitle = $category->getTitle();
        $categoryTree = $this->categoryRepository->categoryFilteredTree($category_id);
        
        foreach ($categoryTree as $category) {
            $childCategories[] = [$category['url'],  $category['title']];
        }
        
        return new ViewModel(["catalog" => $childCategories ?? [] /* $categories */, "title" => $categoryTitle, "id" => $category_id, "breadCrumbs" => $breadCrumbs,]);
    }

     /**
     * Mixed categories page
     * 
     * @route /category[/:id]
     * @return ViewModel
     */
    public function categoryAction($category_id = false)
    {
//        $container = new Container();
//        if ($container->signedUp != true) {
//            return $this->redirect()->toUrl('/my-login');
//        }
        if (empty($category_id)) {
            $category_id = $this->params()->fromRoute('id', '');
        }
        
        if (empty($params = Setting::find(['id' => 'main_menu']))) {
            return $this->responseError404();
        }
        
        $categories = Json::decode($params->getValue(), Json::TYPE_ARRAY);

        if (empty($category = $categories[$category_id])) {
            return $this->responseError404();
        }

        return new ViewModel(["title" => $category["title"],]);
    }

    /**
     * products of brand
     * 
     * @route /brand[/:id][/:category_id]
     * @return ViewModel
     */
    public function brandProductsAction()
    {
//        $container = new Container();
//        if ($container->signedUp != true) {
//            return $this->redirect()->toUrl('/my-login');
//        }
        $brand_id = $this->params()->fromRoute('brand_id', '');
        $category_url = $this->params()->fromRoute('category_id', '');
  
        if (empty($brand = Brand::find(["id" => $brand_id]))) {
            return $this->responseError404();
        }
        
        $brandTitle = $brand->getTitle();
        $categories = $this->getBrandCategories($brand_id);
        $categoryTitle = (empty($category_id)) ? Resource::THE_ALL_PRODUCTS : '';
        $breadCrumbs[] = [null, $brandTitle];
    
        foreach ($categories as $category) {
        
            if ($category->getUrl() == $category_url) {
                $categoryTitle = $category->getTitle();
                $category_id = $category->getId();
            }

            $breadCrumbs[] = [$category->getUrl(), $category->getTitle()];
        }
        
        if (!empty($category_id) and empty($categoryTitle)) {
            $this->getResponse()->setStatusCode(301);
            return $this->redirect()->toUrl('/brand/' . $brand_id);
        }

        return new ViewModel(['breadCrumbs' => $breadCrumbs, 'logo' => $brand->getImage(), 'id' => $brand_id, 'category_id' => $category_id, "title" => $brandTitle, 'category_title' => $categoryTitle,]);
    }

    /**
     * products of seller
     * 
     * @route /seller[/:id][/:category_id]
     * @return ViewModel
     */
    public function providerProductsAction()
    {
//        $container = new Container();
//        if ($container->signedUp != true) {
//            return $this->redirect()->toUrl('/my-login');
//        }
        
        $provider_id = $this->params()->fromRoute('provider_id', '');
        $category_url = $this->params()->fromRoute('category_id', '');
      
        if (empty($provider = Provider::find(["id" => $provider_id]))) {
            return $this->responseError404();
        }
        
        $providerTitle = $provider->getTitle();
        $categories = $this->getProviderCategories($provider_id);
        $categoryTitle = (empty($category_url)) ? Resource::THE_ALL_PRODUCTS : '';
        $breadCrumbs[] = [null, $providerTitle];
    
        foreach ($categories as $category) {
          
            if ($category->getUrl() == $category_url) {
                $categoryTitle = $category->getTitle();
                $category_id = $category->getId();
            }
            
            $breadCrumbs[] = [$category->getUrl(), $category->getTitle()];
        }

        if (!empty($category_id) and empty($categoryTitle)) {
            $this->getResponse()->setStatusCode(301);
            return $this->redirect()->toUrl('/seller/' . $provider_id);
        }
        
        return new ViewModel(['breadCrumbs' => $breadCrumbs, 'logo' => $provider->getImage(), 'id' => $provider_id, 'category_id' => $category_id, "title" => $providerTitle, 'category_title' => $categoryTitle,]);
    }

    /**
     * products of store
     * 
     * @route /store[/:id][/:category_id]
     * @return ViewModel
     */
    public function storeProductsAction()
    {
//        $container = new Container();
//        if ($container->signedUp != true) {
//            return $this->redirect()->toUrl('/my-login');
//        }

        $store_id = $this->params()->fromRoute('store_id', '');
        $category_url = $this->params()->fromRoute('category_id', '');
      
        if (empty($store = Store::find(["id" => $store_id]))) {
            return $this->responseError404();
        }
        
        $provider_id = $store->getProviderId();
        
        if (empty($provider = Provider::find(["id" => $provider_id]))) {
            return $this->responseError404();
        }
        
        $categoryTitle = (empty($category_url)) ? Resource::THE_ALL_PRODUCTS : '';
        $storeTitle = $provider->getTitle();
        $categories = $this->getStoreCategories($store_id);
        $breadCrumbs[] = [null, $storeTitle];

        foreach ($categories as $category) {
           
            if ($category->getUrl() == $category_url) {
                $categoryTitle = $category->getTitle();
                $category_id = $category->getId();
            }
            
            $breadCrumbs[] = [$category->getUrl(), $category->getTitle()];
        }
        if (!empty($category_id) and empty($categoryTitle)) {
            $this->getResponse()->setStatusCode(301);
            return $this->redirect()->toUrl('/store/' . $store_id);
        }

        return new ViewModel(['breadCrumbs' => $breadCrumbs, 'logo' => $provider->getImage(), 'address' => StringHelper::cutAddress($store->getAddress()), 'id' => $store_id, 'category_id' => $category_id, "title" => $storeTitle, 'category_title' => $categoryTitle,]);
    }

    private function getStoreCategories($store_id)
    {
//        $container = new Container();
//        if ($container->signedUp != true) {
//            return $this->redirect()->toUrl('/my-login');
//        }

        $storeProducts = StockBalance::findAll(["where" => ['store_id' => $store_id], 'columns' => ['product_id'], "group" => "product_id"])->toArray();
        $products = ArrayHelper::extractId($storeProducts);
        $storeProductsCategories = $this->productRepository->findAll(["where" => ["id" => $products], 'columns' => ["category_id"], 'group' => ["category_id"]]);
        
        foreach ($storeProductsCategories as $category) {
            $categoriesArray[] = $category->getCategoryId();
        }

        return $this->categoryRepository->findAll(["where" => ["id" => $categoriesArray]]); //->toArray();
    }

    /**
     * 
     * @param string $brand_id
     * @return mixed
     */
    private function getBrandCategories($brand_id)
    {
        $brandProductsCategories = $this->productRepository->findAll(["where" => ["brand_id" => $brand_id], 'columns' => ["category_id"], 'group' => ["category_id"]]);
       
        foreach ($brandProductsCategories as $category) {
            $categoriesArray[] = $category->getCategoryId();
        }

        return $this->categoryRepository->findAll(["where" => ["id" => $categoriesArray]]); //->toArray();
    }

    private function getProviderCategories($provider_id)
    {
//        $container = new Container();
//        if ($container->signedUp != true) {
//            return $this->redirect()->toUrl('/my-login');
//        }
        $brandProductsCategories = $this->productRepository->findAll(["where" => ["provider_id" => $provider_id], 'columns' => ["category_id"], 'group' => ["category_id"]]);
        foreach ($brandProductsCategories as $category) {
            $categoriesArray[] = $category->getCategoryId();
        }

        return $this->categoryRepository->findAll(["where" => ["id" => $categoriesArray]]); //->toArray();
    }

    /**
     * User profile page 
     * 
     * @route /user
     * @return ViewModel
     */
    public function userAction()
    {
//        $container = new Container();
//
//        if ($container->signedUp != true) {
//            return $this->redirect()->toUrl('/my-login');
//        }

        $userId = $this->identity(); //authService->getIdentity();//
        $user = User::find(['id' => $userId]);
        $phone = $user->getPhone();
        $userPaycards = UserPaycard::findAll(['where' => ["user_id" => $userId], "order" => "timestamp desc"]);
        $paycards = ($userPaycards->count()) ? $userPaycards : null;
        $cardInfo = $this->htmlProvider->getUserPayCardInfoService($paycards);

        if (empty($phone)) {
            return $this->unauthorizedLocation();
        }

        $userPhone = StringHelper::phoneFromNum($phone);
        $title = ($user->getName()) ? $user->getName() : "Войти на сайт";

        return new ViewModel(["user" => $user, "userData" => $user->getUserData(), "userPhone" => $userPhone, "title" => $title, "id" => "userid: " . $userId, "bread" => "bread ", "auth" => ($user->getPhone()), "paycards" => $cardInfo,]);
    }

    /**
     * 
     * @param array $params
     * @return string
     */
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

    /**
     *  
     * @param string $productId
     */
    private function addProductToHistory($productId)
    {
        $userId = $this->identity();
        //ProductHistory::remove(['user_id' => $userId, 'product_id' => $productId]);
        $historyItem = ProductHistory::findFirstOrDefault(['user_id' => $userId, 'product_id' => $productId]);
        $historyItem->setUserId($userId)->setProductId($productId)->setTime(time())->persist(['user_id' => $userId, 'product_id' => $productId]);
    }

    /**
     * 
     * @param string $productId
     * @param string $userId
     * @return boolean
     */
    private function isInFavorites($productId, $userId)
    {
        if (!empty($userId)) {
            if (!empty(ProductFavorites::find(['user_id' => $userId, 'product_id' => $productId]))) {
                return true;
            }
        }

        return false;
    }
    
    /*
     * HTTP Error 403
     * 
     * @return ViewModel
     */
    private function unauthorizedLocation()
    {
        $this->getResponse()->setStatusCode(Response::STATUS_CODE_403);
        $vm = new ViewModel();

        return $vm->setTemplate('error/403.phtml');
    }
    
    /*
     * HTTP Error 404
     * 
     * @return ViewModel
     */
    private function responseError404()
    {
        //$response = new Response();
        $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
        //$this->layout('error/404');
        $view = new ViewModel();

        return $view->setTemplate('error/404');
    }

}
