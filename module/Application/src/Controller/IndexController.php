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
use Laminas\Json\Json;

use Application\Service\HtmlProviderService;
use Application\Service\HtmlFormProviderService;
use Application\Resource\StringResource;
use Laminas\Session\Container;
use Application\Adapter\Auth\UserAuthAdapter;
use Laminas\Db\Sql\Where;
use Application\Model\Entity\User;
use Application\Model\Entity\UserData;

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

    public function __construct(TestRepositoryInterface $testRepository, CategoryRepositoryInterface $categoryRepository,
                ProviderRepositoryInterface $providerRepository, StoreRepositoryInterface $storeRepository,
                ProductRepositoryInterface $productRepository, FilteredProductRepositoryInterface $filteredProductRepository,
                BrandRepositoryInterface $brandRepository, ColorRepositoryInterface $colorRepository, SettingRepositoryInterface $settingRepository,
                CharacteristicRepositoryInterface $characteristicRepository,
                PriceRepositoryInterface $priceRepository, StockBalanceRepositoryInterface $stockBalanceRepository,
                HandbookRelatedProductRepositoryInterface $handBookProduct,
                $entityManager, $config, HtmlProviderService $htmlProvider, HtmlFormProviderService $htmlFormProvider, UserRepository $userRepository, AuthenticationService $authService,
                ProductCharacteristicRepositoryInterface $productCharacteristicRepository, BasketRepositoryInterface $basketRepository)
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
    }

    public function onDispatch(MvcEvent $e)
    {
        $userAuthAdapter = new UserAuthAdapter($this->userRepository);
        $result = $this->authService->authenticate($userAuthAdapter);
        $code = $result->getCode();
        if($code != \Application\Adapter\Auth\UserAuthResult::SUCCESS) {
            throw new \Exception('Unknown error in IndexController');
        }

        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);
//        $servicemanager = $e->getApplication()->getServiceManager();
        $userAddressHtml = $this->htmlProvider->writeUserAddress();

//        $this->categoryRepository = $servicemanager->get(CategoryRepositoryInterface::class);
//        $category = $this->categoryRepository->findCategory(29);
//        $e->getApplication()->getMvcEvent()->getViewModel()->setVariable('category', $category );

        // Return the response
        $this->layout()->setVariables([
            'headerText' => $this->htmlProvider->testHtml(),
            'footerText' => 'banzaii',
            'catalogCategoties' => $this->categoryRepository->findAllCategories("", 0, $this->params()->fromRoute('id', '')),
            'userAddressHtml' => $userAddressHtml,
        ]);
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
    
    public function indexAction()
    {
        $container = new Container(StringResource::SESSION_NAMESPACE);
        
//        $userId = $this->identity();
//        $userId = 2;
//        $user = $this->userRepository->find(['id' => $userId]);
//        $userData = new UserData();
//        $userData->setAddress('address');
//        //$userData->setAddress($container->userAddress);
//        //$userData->setGeodata($json);
//        $userData->setGeodata('{}');
//        
//        $where = new Where();
//        $where->equalTo('id', 4);
//        $where->equalTo('user_id', 2);
//        
//        $userData2 = $user->getUserData();
//        
//        $user->deleteUserData(['where' => $where]);
//        foreach($userData2 as $ud) {
//            echo $ud->getId().' '.$ud->getUserId().' '.$ud->getAddress().' '.$ud->getTimestamp(). '<br/>';
//        }
//        exit;

//        $clause = [];
//        foreach($params['characteristics'] as $key=>$value) {
//            //$clause[] = sprintf("( characteristic_id = '%s' and value in(%s) )", $key, implode(',', $value));
//            $clause[] = sprintf("( characteristic_id = '%s' and find_in_set(value, '%s') )", $key, implode(',', $value));
//        }
//        print_r(implode(' or ', $clause));
//        exit;

//        $where = new \Laminas\Db\Sql\Where();
//        list($low, $high) = explode(';', $params['priceRange']);
//        $where->lessThanOrEqualTo('price', $high)->greaterThanOrEqualTo('price', $low);
//        $where->in('category_id', $params['category_id']);
//
//        unset($params['offset']);
//        unset($params['limit']);
//        $params['where'] = $where;
//        
//        $products = $this->handBookRelatedProductRepository->findFilteredProducts($params);
//        foreach($products as $product) {
//            
//            $matchResult = $this->matchProduct($product, $params['characteristics']);
//            if($matchResult) {
//                echo '<pre>';
//                echo $product->getId().' '.$product->getTitle().' '.$product->getPrice()->getPrice(). '<br/>';
//                echo '</pre>';
//            }
//        }
        
//        $products = $this->getProducts($params);
//        foreach($products as $product) {
//            echo '<pre>';
//            echo $product->getId().' '.$product->getTitle().' '.$product->getPrice()->getPrice(). '<br/>';
//            echo '</pre>';
//        }

        return new ViewModel([
            'fooItem' => $container->item
        ]);
    }
    public function basketAction()
    {
            $userId = $this->identity();
            $where = new Where();
            $where->equalTo('user_id', $userId);
            $where->equalTo('order_id', 0);
            /** more conditions come here */
            $columns = ['product_id', 'order_id', 'total'];
            $basket = $this->basketRepository->findAll(['where' => $where, 'columns' => $columns]);
          /* foreach ($basket as $b) {
                if($pId=$b->productId){
                    $product = $this->productRepository->find(['id'=>$pId]);
                    $return['products'][]=[
                        "id" => $pId, 
                        "name" => $product->getTitle(), 
                        "count" => $b->total, 
                        'image'=> $this->productImageRepository->findFirstOrDefault(["product_id"=>$pId])->getHttpUrl(),
                       ]; 
                    $return['total']+=$b->total;
                    $return['count'] ++;
                }    
            }
            exit (print_r($return));*/
        
     $content = $this->htmlProvider->basketData($basket);   
     return new ViewModel([
           /* "providers" => $providers,*/
            "content" => $content,
            "title" => "Корзина",
            
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
        $params['equal']=$product_id;
        
        $products = $this->productRepository->filterProductsByStores($params);
        
        $productPage = $this->htmlProvider->productPage($products);
        $categoryId= $productPage['categoryId'];
        
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
    
    
    
    public function catalogAction($category_id=false)
    {
        if(!$category_id) $category_id=$this->params()->fromRoute('id', '');
        
        try {
            $categoryTitle = $this->categoryRepository->findCategory(['id' => $category_id])->getTitle();
        }
        catch (\Exception $e) {
            header("HTTP/1.1 301 Moved Permanently"); header("Location:/"); exit();
            //$categoryTitle = "&larr;Выбери категорию товаров  ";   $returnProductFilter="";
        }
        if (!$categoryTitle) { 
            header("HTTP/1.1 301 Moved Permanently"); header("Location:/"); exit();
            //$categoryTitle = "&larr;Выбери категорию товаров  ";   $returnProductFilter=""; 
            
        }
        
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $filtrForCategory=$container->filtrForCategory;
        if(!$filtred=$filtrForCategory[$category_id]['fltr']) {
            $filtred=[];
        }
        $categories = $this->categoryRepository->findAllCategories("", 0, $category_id);
        $matherCategories = $this->categoryRepository->findAllMatherCategories($category_id);
        //$matherCategories[]=[0=>$category_id];
        $bread = $this->htmlProvider->breadCrumbs($matherCategories);
        $breadmenu = $this->htmlProvider->breadCrumbsMenu($matherCategories);
        $categoryTree = $this->categoryRepository->findCategoryTree($category_id, [$category_id]);
        $orders=["","pr.title ABS", 'price ABS','price DESC',"pr.title DESC"];
        $params['order']=$orders[$filtrForCategory[$category_id]['sortOrder']];
        $params['filter'] = $filtred;
        $products = $this->productRepository->filterProductsByStores($params);
        $filteredProducts = $this->productRepository->filterProductsByCategories($products, $categoryTree);
        $returnProduct .= $this->htmlProvider->productCard($filteredProducts,$category_id)['card'];
       
        $minMax= $this->handBookRelatedProductRepository->findMinMaxPriceValueByCategory($categoryTree);
        //$minMax = ['minprice' => 500000, 'maxprice' => 9000000];
        //exit(print_r($minMax));
        $filters = $this->productCharacteristicRepository->getCategoryFilter($matherCategories);
        $filterForm = $this->htmlProvider->getCategoryFilterHtml($filters, $category_id, $minMax);

        


        
        $myKey=(is_array($filtrForCategory))?$filtrForCategory[$category_id]['sortOrder']:0;
        $hasRest = (is_array($filtrForCategory))?$filtrForCategory[$category_id]['hasRestOnly']:0;
        $vwm=[
            "catalog" => $categories,
            "title" => $categoryTitle,//."/$category_id",
            "id" => $category_id,
            "bread"=> $bread,
            'priducts'=> $returnProduct,
            'sortselect' =>[$myKey=> " selected "],
            'hasRestOnly' =>[ $hasRest => " checked "],
            'filterform'=> $filterForm,
            'breadmenu' => $breadmenu,
        ];
        return new ViewModel($vwm);

    }
    
    public function userAction($category_id=false)
    {
        $userId = $this->identity();//authService->getIdentity();//
        $user = $this->userRepository->find(['id'=>$userId]);
        $userData = $user->getUserData();
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
