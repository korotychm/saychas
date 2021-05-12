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
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
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
    private $handBookRelatedProductRepository;
    private $entityManager;
    private $config;
    private $htmlProvider;
    private $htmlFormProvider;

    public function __construct(TestRepositoryInterface $testRepository, CategoryRepositoryInterface $categoryRepository,
                ProviderRepositoryInterface $providerRepository, StoreRepositoryInterface $storeRepository,
                ProductRepositoryInterface $productRepository, FilteredProductRepositoryInterface $filteredProductRepository, BrandRepositoryInterface $brandRepository, 
                CharacteristicRepositoryInterface $characteristicRepository,
                PriceRepositoryInterface $priceRepository, StockBalanceRepositoryInterface $stockBalanceRepository,
                HandbookRelatedProductRepositoryInterface $handBookProduct,
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
        $this->handBookRelatedProductRepository = $handBookProduct;
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
            'catalogCategoties' => $this->categoryRepository->findAllCategories("", 0, $this->params()->fromRoute('id', '')),
            'userAddressHtml' => $userAddressHtml,
        ]);
        //$this->layout()->setTemplate('layout/mainpage');
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
        $this->layout()->setTemplate('layout/mainpage');
        $categories = $this->categoryRepository->findAllCategories();
        return new ViewModel([
            'menu' => $categories
        ]);
    }

    public function providerAction()
    {
        $id=$this->params()->fromRoute('id', '');
        $this->layout()->setTemplate('layout/mainpagenew');
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
       
         $this->layout()->setTemplate('layout/mainpagenew');
        $product_id=$this->params()->fromRoute('id', '');
        $params['equal']=$product_id;
        $products = $this->productRepository->filterProductsByStores($params);
        $productPage = $this->htmlProvider->productPage($products);
        $categoryId= $productPage['categoryId'];
        //$this->layout()->setTemplate('layout/mainpage');
        $container = new Container(StringResource::SESSION_NAMESPACE);
        //$addresForm = "". $this->htmlProvider->inputUserAddressForm(['seseionUserAddress'=>$container-> seseionUserAddress]);
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
//            'addressform'=> $addresForm."",
        ];
        return new ViewModel($vwm);
      }
    
    public function catalogAction()
    {
        $category_id=$this->params()->fromRoute('id', '');

        $this->layout()->setTemplate('layout/mainpagenew');
        $container = new Container(StringResource::SESSION_NAMESPACE);
        //$addresForm = "". $this->htmlProvider->inputUserAddressForm(['seseionUserAddress'=>$container-> seseionUserAddress]);
        $filtrForCategory=$container->filtrForCategory;
        //        $params['filter'] = ['000000003', '000000013', '000000014'];
        //$filtrForCategory[$category_id]['fltr'] = ['000000003', '000000013', '000000014'];
        if(!$filtred=$filtrForCategory[$category_id]['fltr']) {
            $filtred=[];
        }
        
        $categories = $this->categoryRepository->findAllCategories("", 0, $category_id);
        $matherCategories = $this->categoryRepository->findAllMatherCategories($category_id);
        $bread = $this->htmlProvider->breadCrumbs($matherCategories);
         
     
        $categoryTree = $this->categoryRepository->findCategoryTree($category_id, [$category_id]);
        //$params['egualTo'] = ['id'=>$category_id];
        //$params['or'];// = ['id'=>$category_id];
        //$params['in'] = ['000000003', '000000004', '000000005', '000000001', '000000002'];
        $orders=["","pr.title ABS", 'price ABS','price DESC',"pr.title DESC"];
        $params['order']=$orders[$filtrForCategory[$category_id]['sortOrder']];
        //$params['limit']=4;
        //print_r($filtred);
        $params['filter'] = $filtred;
//        print_r($params['filter']);
//        echo '<br/>';
//        exit;
        $products = $this->productRepository->filterProductsByStores($params);
        //$products = $this->handBookRelatedProductRepository->findAll(['where' => $this->packParams(['filter' => $params['filter'] ]) ]);
        $filteredProducts = $this->productRepository->filterProductsByCategories($products, $categoryTree);
        $returnProduct .= $this->htmlProvider->productCard($filteredProducts,$category_id)['card'];
        $returnProductFilter = $this->htmlProvider->productCard($filteredProducts,$category_id)['filter'];
        //$filterForm = 
        $filterArray = $this->htmlProvider ->getCategoryFilterArray($returnProductFilter, $matherCategories );//
        $filtr= $this->characteristicRepository->getCharacteristicFromList(join(",",$returnProductFilter), ['where'=>$filterArray]);
        
        $filterForm =  
                //print_r($filtr, true); //
                $this->htmlProvider ->getCategoryFilterHtml($filtr, $category_id);
                
                
        
        
        
        try {
            $categoryTitle = $this->categoryRepository->findCategory(['id' => $category_id])->getTitle();
        }
        catch (\Exception $e) {
            $categoryTitle = "&larr;Выбери категорию товаров  ";   $returnProductFilter="";
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
            //'filter' =>  print_r($returnProductFilter,true),
            'addressform'=> $addresForm."",
            'sortselect' =>[$myKey=> " selected "],
            'hasRestOnly' =>[ $hasRest => " checked "],
            'filterform'=> $filterForm,
            //print_r($bread,true),
        ];
        //exit (print_r($vwm));   
        
        return new ViewModel($vwm);

    }
    
    public function catalog1Action()
    {
        $category_id=$this->params()->fromRoute('id', '');

        $this->layout()->setTemplate('layout/mainpage');
        
        $container = new Container(StringResource::SESSION_NAMESPACE);
        
        $filtrForCategory=$container->filtrForCategory;
        
        if(!$filtred=$filtrForCategory[$category_id]['fltr']) {
            $filtred=[];
        }
        
        $categories = $this->categoryRepository->findAllCategories("", 0, $category_id);
        $matherCategories = $this->categoryRepository->findAllMatherCategories($category_id);
        $bread = $this->htmlProvider->breadCrumbs($matherCategories);
     
        $categoryTree = $this->categoryRepository->findCategoryTree($category_id, [$category_id]);
        $orders=["","pr.title ABS", 'price ABS','price DESC',"pr.title DESC"];
        $params['order']=$orders[$filtrForCategory[$category_id]['sortOrder']];
        $params['filter'] = $filtred;

        $products = $this->productRepository->filterProductsByStores($params);
//        $products = $this->handBookRelatedProductRepository->findAll(['where' => $this->packParams(['filter' => $params['filter'] ]) ]);

//        $printed = '';
//        foreach ($prods as $p1) {
//            if($printed != $p1->getId()) {
//                echo $p1->getId(). ' '. $p1->title.' '.$p1->getProviderId().'<br/>';
//                $printed = $p1->getId();
//            }
//        }
//        echo '<hr/>';
//        echo 'findAll<br/>';
//        $products = $this->handBookRelatedProductRepository->findAll(['where' => $this->packParams(['filter' => $params['filter'] ]) ]);
//        foreach($products as $p) {
//            echo $p->getId(). ' '. $p->getTitle().' '.$p->getProviderId(). '<br/>';
//            echo $p->getProvider()->getStores()->current()->getId().'<br/>';
//        }
//        echo '<hr/>';
//        exit;

        $filteredProducts = $this->productRepository->filterProductsByCategories($products, $categoryTree);
        $returnProduct .= $this->htmlProvider->productCard($filteredProducts,$category_id)['card'];
        $returnProductFilter = $this->htmlProvider->productCard($filteredProducts,$category_id)['filter'];
        //$filterForm = 
        $filterArray = $this->htmlProvider ->getCategoryFilterArray($returnProductFilter, $matherCategories );//
        $filtr= $this->characteristicRepository->getCharacteristicFromList(join(",",$returnProductFilter), ['where'=>$filterArray]);
        
        $filterForm =  
                $this->htmlProvider ->getCategoryFilterHtml($filtr, $category_id);

        try {
            $categoryTitle = $this->categoryRepository->findCategory(['id' => $category_id])->getTitle();
        }
        catch (\Exception $e) {
            $categoryTitle = "&larr;Выбери категорию товаров  ";   $returnProductFilter="";
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
//            'addressform'=> $addresForm."",
            'sortselect' =>[$myKey=> " selected "],
            'hasRestOnly' =>[ $hasRest => " checked "],
            'filterform'=> $filterForm,
        ];
        
        return new ViewModel($vwm);

    }
    
    
}
