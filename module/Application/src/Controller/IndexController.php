<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Mvc\MvcEvent;
use Laminas\Authentication\AuthenticationService;
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
use Application\Model\Entity\ClientOrder;
use Application\Model\Entity\Delivery;
use Laminas\Json\Json;

use Application\Service\HtmlProviderService;
use Application\Service\HtmlFormProviderService;
use Application\Resource\StringResource;
use Laminas\Session\Container;// as SessionContainer;
use Laminas\Session\SessionManager;
use Application\Adapter\Auth\UserAuthAdapter;
use Laminas\Db\Sql\Where;
use Application\Model\Entity\User;
use Application\Model\Entity\UserData;
use Application\Helper\StringHelper;

use Application\Model\Entity\ProductFavorites;
use Application\Model\Entity\ProductHistory;

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
    private $entityManager;
    private $config;
    private $htmlProvider;
    private $htmlFormProvider;
    private $userRepository;
    private $authService;
    private $productCharacteristicRepository;
    private $colorRepository;
    private $basketRepository;
    //private $sessionContainer;
    private $sessionManager;

    
    public function __construct(TestRepositoryInterface $testRepository, CategoryRepositoryInterface $categoryRepository,
                ProviderRepositoryInterface $providerRepository, StoreRepositoryInterface $storeRepository,
                ProductRepositoryInterface $productRepository, FilteredProductRepositoryInterface $filteredProductRepository,
                BrandRepositoryInterface $brandRepository, ColorRepositoryInterface $colorRepository, SettingRepositoryInterface $settingRepository,
                CharacteristicRepositoryInterface $characteristicRepository,
                PriceRepositoryInterface $priceRepository, StockBalanceRepositoryInterface $stockBalanceRepository,
                HandbookRelatedProductRepositoryInterface $handBookProduct,
                $entityManager, $config, HtmlProviderService $htmlProvider, HtmlFormProviderService $htmlFormProvider, UserRepository $userRepository, AuthenticationService $authService,
                ProductCharacteristicRepositoryInterface $productCharacteristicRepository, BasketRepositoryInterface $basketRepository/*, $sessionContainer*/, $sessionManager)
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
        $this->htmlFormProvider = $htmlFormProvider;
        $this->userRepository = $userRepository;
        $this->authService = $authService;
        $this->productCharacteristicRepository = $productCharacteristicRepository;
        $this->basketRepository = $basketRepository;
//        $this->sessionContainer = $sessionContainer;
        $this->sessionManager = $sessionManager;
        
        $this->entityManager->initRepository(ClientOrder::class);
        $this->entityManager->initRepository(Delivery::class);
        
    }

    public function onDispatch(MvcEvent $e)
    {
        //SessionContainer::setDefaultManager($this->sessionManager);
        //$expirationSeconds = $this->config['session_config']['cookie_lifetime'];
        //$this->sessionContainer->setExpirationSeconds($expirationSeconds/*, 'userIdentity'*/);
        $userAuthAdapter = new UserAuthAdapter(/*$this->userRepository*//*$this->sessionContainer*/);
        $result = $this->authService->authenticate($userAuthAdapter);
        $code = $result->getCode();
        if($code != \Application\Adapter\Auth\UserAuthResult::SUCCESS) {
            throw new \Exception('Unknown error in IndexController');
        }
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);
//        $servicemanager = $e->getApplication()->getServiceManager();
        
        $userId = $this->identity();
        $user = $this->userRepository->find(['id'=>$userId]);
        
        
        
        $userAddressHtml = $this->htmlProvider->writeUserAddress($user);
        $userInfo = $this->htmlProvider->getUserInfo($user);
       

//        $this->categoryRepository = $servicemanager->get(CategoryRepositoryInterface::class);
//        $category = $this->categoryRepository->findCategory(29);
//        $e->getApplication()->getMvcEvent()->getViewModel()->setVariable('category', $category );

        $addressLegal = ($userInfo["userAddress"])?true:false;
        $userLegal = ($userInfo["userid"] and $userInfo["phone"])?true:false;
        
        
        // Return the response
        $this->layout()->setVariables([
            'headerText' => $this->htmlProvider->testHtml(),
            'footerText' => 'banzaii',
            'catalogCategoties' => $this->categoryRepository->findAllCategories("", 0, $this->params()->fromRoute('id', '')),
            'userAddressHtml' => $userAddressHtml,
            'addressLegal' =>  $addressLegal,
            'userLegal' =>  $userLegal,
            'username' =>  $userInfo['name'],
            'userphone' =>  $userInfo['phone'],
        ]);
        $this->layout()->setVariable('banzaii', 'vonzaii');
        //$this->layout()->setTemplate('layout/mainpage');
        return $response;

    }
    
    private function matchProduct(HandbookRelatedProduct $product, $characteristics)
    {
        $flags = [];
        foreach($characteristics as $key => $value) {
            $found = $this->productCharacteristicRepository->find(['characteristic_id' => $key, 'product_id' => $product->getId() ]);
            if(null == $found) {
                $flags[$key] = false;
                continue;
            }
            $type = $found->getType();
            switch($type) {
                case CharacteristicRepository::INTEGER_TYPE:
                    list($left, $right) = explode(';', $value[0]);
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
        foreach($flags as $f) {
            if(!$f) {
                return false;
            }
        }
        return true;
    }

    private function getProducts($params)
    {
        $where = new \Laminas\Db\Sql\Where();
        list($low, $high) = explode(';', $params['priceRange']);
        $where->lessThanOrEqualTo('price', $high)->greaterThanOrEqualTo('price', $low);
        $where->equalTo('category_id', $params['category_id']);
        //$where->in('category_id', $params['category_id']);

        unset($params['offset']);
        unset($params['limit']);
        $params['where'] = $where;
        
        $products = $this->handBookRelatedProductRepository->findAll($params);
        $filteredProducts = [];
        foreach($products as $product) {
            
            $matchResult = $this->matchProduct($product, $params['characteristics']);
            if($matchResult) {
                $filteredProducts[] = $product;
            }
        }
        return $filteredProducts;
    }
    
    public function clientOrdersAction()
    {
        $userId = $this->identity();
        $user = User::find(['id' => $userId]);
        $userData = $user->getUserData();
        $userPhone = $user->getPhone();
        if (!$userPhone) {
              $this->getResponse()->setStatusCode(403);
              $vw = new ViewModel();
              $vw->setTemplate('error/403.phtml');
              return  $vw;
        }
        $orders = ClientOrder::findAll(['user_id'=>$userId]);
        if (!empty($orders)){
            
            $orderList = $this->htmlProvider->orderList($orders);
   
        } 
        else {
            $orderList = StringResource::ORDER_EMPTY;
                    
        }    
        $orderList = "<pre>".print_r($orderList,true)."</pre>";    
        
        return new ViewModel([
            'title' => StringResource::ORDER_TITLE, //  $container->item
            'orders'=> $orderList,
        ]);
    }
    
    
    public function indexAction()
    {
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
        
        //$container = $this->sessionContainer;// new Container(StringResource::SESSION_NAMESPACE);
        $container = new Container(StringResource::SESSION_NAMESPACE);
//        if(isset($container->item)) {
//            print_r($container->item);
//        }else{
//            print_r('null');
//        }
//        exit;
        return new ViewModel([
            'fooItem' => 'banzaii', //  $container->item
        ]);
    }
    
    
    
    
    public function basketAction()
    {
            $basketUser['id'] = $userId = $this->identity();
            $user = $this->userRepository->find(['id'=>$userId]);
             $basketUser['userId']= $user->getUserId();
            $basketUser['phone'] = $user->getPhone();
            $basketUser['name'] = $user->getName();
            $userData = $user->getUserData();
            $count = $userData->count();
            if ($count <=0){
                /*header("HTTP/1.1 301 Moved Permanently");
                header("Location: /");
                exit();   */
            }
            else
            {
                $basketUser['address'] = $userData->current()->getAddress();
                $basketUser['geodata'] = $userData->current()->getGeoData();
            
            }    
            $legalUser=true; 
            
            
            if(!$basketUser['phone'] or !$basketUser['name']   ){
                $legalUser=false; 
            }
            $basketUser['phoneformated'] = StringHelper::phoneFromNum($basketUser['phone']);
            
            $where = new Where();
            $where->equalTo('user_id', $userId);
            $where->equalTo('order_id', 0);
            /** more conditions come here */
            $columns = ['product_id', 'order_id', 'total', 'price'];
            $basket = $this->basketRepository->findAll(['where' => $where, 'columns' => $columns]);
          
        
     $content = $this->htmlProvider->basketData($basket);   
     //exit (print_r($content));
     return new ViewModel([
           /* "providers" => $providers,*/
            "content" => $content["product"],
            "title" => "Корзина",   
            "titleH" => $content["title"],
            "basketUser" => $basketUser, 
            "cardinfo" => "4276 5555 **** <span class='red'>1234&darr;</span>",
            "countproviders" => $content["countproviders"],
            "countprducts" => $content["countproducts"],
            "legalUser" => $legalUser,
            "legalAddress" => $legalAddress,  
            'textdefault' => \Application\Resource\StringResource::BASKET_SAYCHAS_do.", ",
            "register_title" => StringResource::MESSAGE_ENTER_OR_REGISTER_TITLE,
            "register_text" => StringResource::MESSAGE_ENTER_OR_REGISTER_TEXT,
            
        ]);   
    }
    
    public function previewAction()
    {
        //$this->layout()->setTemplate('layout/mainpage');
        $categories = $this->categoryRepository->findAllCategories();
        return new ViewModel([
            'menu' => $categories
        ]);
    }

    public function providerAction()
    {
        $id=$this->params()->fromRoute('id', '');
        //$this->layout()->setTemplate('layout/mainpagenew');
        $categories = $this->categoryRepository->findAllCategories("", 0, $id);
       /* $providers = $this->providerRepository->findAll(['table'=>'provider', 'limit' => 100, 'order'=>'id ASC', 'offset' => 0]);*/
        return new ViewModel([
           /* "providers" => $providers,*/
            "catalog" => $categories,
        ]);

    }

    
    private function packParams($params)
    {
        $a = [];
        foreach($params['filter'] as $p) {
           $a[] = "find_in_set('$p', param_value_list)";
        }
        $res = ' 1';
        if(count($a) > 0) {
            $res = '('.implode(' OR ', $a).')';
        }
        return $res;
    }

    public function productAction()
    {
        $product_id=$this->params()->fromRoute('id', '');
        if (empty($product_id)) {header("location:/"); exit();}
        $params['equal']=$product_id;
        
        $products = $this->productRepository->filterProductsByStores($params);
        if (empty($products)) {header("location:/"); exit();}
        
        $productPage = $this->htmlProvider->productPage($products);
        $categoryId= $productPage['categoryId'];
        
        //$container = $this->sessionContainer;// new Container(StringResource::SESSION_NAMESPACE);
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $filtrForCategory=$container->filtrForCategory;
        $categories = $this->categoryRepository->findAllCategories("", 0, $categoryId);
        $bread = $this->categoryRepository->findAllMatherCategories($categoryId);
        $bread = $this->htmlProvider->breadCrumbs($bread);
        $categoryTitle = $this->categoryRepository->findCategory(['id' => $categoryId])->getTitle();
        $vwm=[
            'id' => $product_id,
            'catalog' => $categories,
            'title' => $productPage['title'],
            'category' => $categoryTitle,
            'bread'=> $bread,
            'product'=> $productPage['card'],
            'filter' =>  $returnProductFilter,
        ];
        return new ViewModel($vwm);
      }
      
      
      
      public function productPageAction()
    {
        $product_id=$this->params()->fromRoute('id', '');
        if (empty($product_id)) {header("location:/"); exit();}
        
        $params['equal']=$product_id;        
        $products = $this->productRepository->filterProductsByStores($params);
        if (empty($products)) {header("location:/"); exit();}
        
        $productPage = $this->htmlProvider->productPageService($products);
        $categoryId= $productPage['categoryId'];
        
        //$container = $this->sessionContainer;// new Container(StringResource::SESSION_NAMESPACE);
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $filtrForCategory=$container->filtrForCategory;
        $categories = $this->categoryRepository->findAllCategories("", 0, $categoryId);
        $bread = $this->categoryRepository->findAllMatherCategories($categoryId);
        $bread = $this->htmlProvider->breadCrumbs($bread);
        $categoryTitle = $this->categoryRepository->findCategory(['id' => $categoryId])->getTitle();
        $vwm=[
            'id' => $product_id,
            'catalog' => $categories,
            'title' => $productPage['title'],
            'images' => $productPage['images'],
            'category' => $categoryTitle,
            'bread'=> $bread,
            'characteristics' => $productPage["characteristics"],
            'product'=> $productPage['card'],
            'filter' =>  $returnProductFilter,
        ];
        return new ViewModel($vwm);
      }
    
      
      
      
    
    public function catalogAction($category_id = false)
    {
        if(!$category_id) {
            $category_id=$this->params()->fromRoute('id', '');
        }
        
        try {
            $categoryTitle = $this->categoryRepository->findCategory(['id' => $category_id])->getTitle();
        }
        catch (\Exception $e) {
            header("HTTP/1.1 301 Moved Permanently"); header("Location:/"); exit();
        }
        if (!$categoryTitle) { 
            header("HTTP/1.1 301 Moved Permanently"); header("Location:/"); exit();
        }
        
//        $container = new Container(StringResource::SESSION_NAMESPACE);
        
        $categories = $this->categoryRepository->findAllCategories("", 0, $category_id);
        $matherCategories = $this->categoryRepository->findAllMatherCategories($category_id);
        $bread = $this->htmlProvider->breadCrumbs($matherCategories);
        $breadmenu = $this->htmlProvider->breadCrumbsMenu($matherCategories);

        $categoryTree = $this->categoryRepository->findCategoryTree($category_id, [$category_id]);
        
        $minMax= $this->handBookRelatedProductRepository->findMinMaxPriceValueByCategory($categoryTree);
        $filters = $this->productCharacteristicRepository->getCategoryFilter($matherCategories);
        $filterForm = $this->htmlProvider->getCategoryFilterHtml($filters, $category_id, $minMax);
        
        $vwm=[
            "catalog" => $categories,
            "title" => $categoryTitle,
            "id" => $category_id,
            "bread"=> $bread,
            'filterform'=> $filterForm,
            'breadmenu' => $breadmenu,
        ];
        return new ViewModel($vwm);
    }
          
    public function userAction($category_id=false)
    {
        $userId = $this->identity();//authService->getIdentity();//
        // $user = $this->userRepository->find(['id'=>$userId]);
        $user = User::find(['id' => $userId]);
        $userData = $user->getUserData();
        $userPhone = $user->getPhone();
        //$userPhone =  StringHelper::phoneFromNum($user->getPhone());
        if (!$userPhone) {
              $this->getResponse()->setStatusCode(403);
              $vw = new ViewModel();
              $vw->setTemplate('error/403.phtml');
              return  $vw;
        }
//        else exit ($userPhone);
        $userPhone =  StringHelper::phoneFromNum($userPhone);
        
        //
        $title=($user->getName())?$user->getName():"Войти на сайт";
        /* НАДО!!!
         * 
         * $user->getPassword();
         $userData = getTimestamp()
         * 
         * 
         */
        
        $vwm=[
            //"catalog" => $categories,
            "user" => $user,
            "userData" => $userData,
            "userPhone" => $userPhone,
            "title" => $title ,//."/$category_id",
            "id" => "userid: ".$userId,
            "bread" => "bread $bread",
            "auth"=> ($user->getPhone()),
            "userdata" => "<ul>$content</ul>",
            
        ];
        return new ViewModel($vwm);

    }
}



//        $params = [
//            'category_id' => '000000006',
//            'offset' => 0,
//            'limit' => 1111,
//            'priceRange' => '580000;8000000',//5399100',//3399100',//1210000','5399100;5399100',
//            'characteristics' => [
//                '000000001-000000006' => [
//                    '156',
//                    '704',
//                ],
////
////                '000000003-000000006' => [
////                    '000003',
////                    '000011',
////                ],
////
//                '000000004-000000006' => [
//                    '000000002',
//                    '000000004',
//                    '000000011',
//                    '000000012',
//                ],
////
//                '000000014-000000006' => [
//                    '000000011',
//                    '000000044',
//                ],
////
////                '000000029-000000006' => [
////                    '6.2;6.6',
////                ],
////
////                '000000036-000000006' => [
////                    '000000040',
////                    '000000041',
////                ],
////
////                '000000037-000000006' => [
////                    '000000017',
////                    '000000042',
////                ],
////
////                '000000040-000000006' => [
////                    '000000021',
////                    '000000022',
////                ],
////
////                '000000041-000000006' => [
////                    '000000024',
////                    '000000025',
////                ],
//
////                '000000051-000000006' => [
////                        '000000034',
////                        '000000035',
////                ],
////
////                '000000058-000000006' => [
////                    '21;93'
////                ],
//            ],
//        ];
