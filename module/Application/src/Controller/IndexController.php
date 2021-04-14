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
use Laminas\Mvc\MvcEvent;
use Application\Model\TestRepositoryInterface;
use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
use Application\Model\RepositoryInterface\ProviderRepositoryInterface;
use Application\Model\RepositoryInterface\StoreRepositoryInterface;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;
use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;
use Application\Model\RepositoryInterface\BrandRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Application\Service\HtmlProviderService;
use Application\Service\HtmlFormProviderService;
use Application\Resource\StringResource;
use Laminas\Session\Container;

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
    private $characteristicRepository;
    private $priceRepository;
    private $stockBalanceRepository;
    private $entityManager;
    private $config;
    private $htmlProvider;
    private $htmlFormProvider;

    public function __construct(TestRepositoryInterface $testRepository, CategoryRepositoryInterface $categoryRepository,
                ProviderRepositoryInterface $providerRepository, StoreRepositoryInterface $storeRepository,
                ProductRepositoryInterface $productRepository, FilteredProductRepositoryInterface $filteredProductRepository, BrandRepositoryInterface $brandRepository, 
                CharacteristicRepositoryInterface $characteristicRepository,
                PriceRepositoryInterface $priceRepository, StockBalanceRepositoryInterface $stockBalanceRepository,
            $entityManager, $config, HtmlProviderService $htmlProvider, HtmlFormProviderService $htmlFormProvider)
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
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->htmlProvider = $htmlProvider;
        $this->htmlFormProvider = $htmlFormProvider;
    }

    public function onDispatch(MvcEvent $e) 
    {
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
            'userAddressHtml' => $userAddressHtml,
        ]);
        $this->layout()->setTemplate('layout/mainpage');
        return $response;
        
    }
      
    public function indexAction()
    {
        $container = new Container(StringResource::SESSION_NAMESPACE);


        return new ViewModel([
            'fooItem' => $container->item
        ]);
    }
    
    public function previewAction()
    {
        $this->layout()->setTemplate('layout/preview');
        $categories = $this->categoryRepository->findAllCategories();
        return new ViewModel([
            'menu' => $categories
        ]);
    }

    public function providerAction()
    {
        $id=$this->params()->fromRoute('id', '');
        $this->layout()->setTemplate('layout/mainpage');
        $categories = $this->categoryRepository->findAllCategories("", 0, $id);
        $providers = $this->providerRepository->findAll(['table'=>'provider', 'limit' => 100, 'order'=>'id ASC', 'offset' => 0]);
        return new ViewModel([
            "providers" => $providers,
            "catalog" => $categories,
        ]);
        
    }
   
    public function productAction()
    {
        $product_id=$this->params()->fromRoute('id', '');
        
        
        $params['in'] = ['000000003', '000000004', '000000005', '000000001', '000000002'];
       /* $orders=["","pr.title ABS", 'price ABS','price DESC',"pr.title DESC"];
        $params['order']=$orders[$filtrForCategory[$category_id]['sortOrder']];*/
        $params['equal']=$product_id;
        
        //exit ($params['equal']);
        //$params['limit']=4;
        $products = $this->productRepository->filterProductsByStores($params);
        
        $productPage = $this->htmlProvider->productPage($products);
                $categoryId= $productPage['categoryId'];
        
        //$categoty_id = $this->htmlProvider->productPage($products)->categoryId;
       //exit ($categoty_id);
        
        //$category_id=0 ; //$products->getCategoryId() ; 
        
        $this->layout()->setTemplate('layout/mainpage');
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $addresForm = "". $this->htmlProvider->inputUserAddressForm(['seseionUserAddress'=>$container-> seseionUserAddress]);
        
        $filtrForCategory=$container->filtrForCategory;
        $categories = $this->categoryRepository->findAllCategories("", 0, $categoryId);
        $bread = $this->categoryRepository->findAllMatherCategories($categoryId);
        $bread = $this->htmlProvider->breadCrumbs($bread);
         
        
        /*$categoryTree = $this->categoryRepository->findCategoryTree($category_id);
        //$categoryTree[] = $category_id;
        $params['in'] = ['000000003', '000000004', '000000005', '000000001', '000000002'];
        $orders=["","pr.title ABS", 'price ABS','price DESC',"pr.title DESC"];
        $params['order']=$orders[$filtrForCategory[$category_id]['sortOrder']];
        //$params['limit']=4;
        /*$products = $this->productRepository->filterProductsByStores($params);
        $filteredProducts = $this->productRepository->filterProductsByCategories($products, $categoryTree);
        $returnProduct= $this->htmlProvider->productCard($filteredProducts,$category_id)->card;*/
        
        
        
        //$myKey=(is_array($filtrForCategory))?$filtrForCategory[$category_id]['sortOrder']:0;
        //$hasRest = (is_array($filtrForCategory))?$filtrForCategory[$category_id]['hasRestOnly']:0; 
        $vwm=[
        
            "id" => $product_id,
            "catalog" => $categories,
           "title" => $productPage['title'],
            //"id" => $category_id,
            "bread"=> $bread,
            'product'=> $productPage['card'],
            'filter' =>  $returnProductFilter,
            'addressform'=> $addresForm."",
           /* 'sortselect' =>[$myKey=> " selected "],
            'hasRestOnly' =>[ $hasRest => " checked "],*/
            //print_r($bread,true),
        ];
        //exit (print_r($vwm));   
        
        return new ViewModel($vwm);
    
        
    }
    
    public function catalogAction()
    {
        $category_id=$this->params()->fromRoute('id', '');
        $this->layout()->setTemplate('layout/mainpage');
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $addresForm = "". $this->htmlProvider->inputUserAddressForm(['seseionUserAddress'=>$container-> seseionUserAddress]);
        $filtrForCategory=$container->filtrForCategory;
        
        $categories = $this->categoryRepository->findAllCategories("", 0, $category_id);
        $bread = $this->categoryRepository->findAllMatherCategories($category_id);
        $bread = $this->htmlProvider->breadCrumbs($bread);
         
     
        $categoryTree = $this->categoryRepository->findCategoryTree($category_id, [$category_id]);
        //$params['egualTo'] = ['id'=>$category_id];
        //$params['or'];// = ['id'=>$category_id];
        $params['in'] = ['000000003', '000000004', '000000005', '000000001', '000000002'];
        $orders=["","pr.title ABS", 'price ABS','price DESC',"pr.title DESC"];
        $params['order']=$orders[$filtrForCategory[$category_id]['sortOrder']];
        //$params['limit']=4;
        $products = $this->productRepository->filterProductsByStores($params);
        $filteredProducts = $this->productRepository->filterProductsByCategories($products, $categoryTree);
        $returnProduct.= $this->htmlProvider->productCard($filteredProducts,$category_id)->card;
        $returnProductFilter.="ПП характеристики в категории: ". $this->htmlProvider->productCard($filteredProducts,$category_id)->filter;
        try {
            $categoryTitle = $this->categoryRepository->findCategory(['id' => $category_id])->getTitle();
        }
        catch (\Exception $e) {
            $categoryTitle = "&larr;Выбери категорию товаров  ";   $returnProductFilter=[];
        }     
        
        
        if (!$categoryTitle) { $categoryTitle = "&larr;Выбери категорию товаров  ";   $returnProductFilter=""; }
        $myKey=(is_array($filtrForCategory))?$filtrForCategory[$category_id]['sortOrder']:0;
        $hasRest = (is_array($filtrForCategory))?$filtrForCategory[$category_id]['hasRestOnly']:0; 
        $vwm=[
        
            "catalog" => $categories,
            "title" => $categoryTitle,//."/$category_id",
            "id" => $category_id,
            "bread"=> $bread,
            'priducts'=> $returnProduct,
            'filter' =>  $returnProductFilter,
            'addressform'=> $addresForm."",
            'sortselect' =>[$myKey=> " selected "],
            'hasRestOnly' =>[ $hasRest => " checked "],
            //print_r($bread,true),
        ];
        //exit (print_r($vwm));   
        
        return new ViewModel($vwm);

    }
    
}
