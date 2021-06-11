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
use Laminas\Authentication\AuthenticationService;
use Application\Model\TestRepositoryInterface;
use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
use Application\Model\RepositoryInterface\ProviderRepositoryInterface;
use Application\Model\RepositoryInterface\StoreRepositoryInterface;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;
use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;
use Application\Model\RepositoryInterface\BrandRepositoryInterface;
use Application\Model\RepositoryInterface\ColorRepositoryInterface;
use Application\Model\RepositoryInterface\SettingRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Application\Model\Entity\ProductCharacteristic;
use Application\Model\RepositoryInterface\ProductCharacteristicRepositoryInterface;
use Application\Model\Repository\UserRepository;

use Application\Service\HtmlProviderService;
use Application\Service\HtmlFormProviderService;
use Application\Resource\StringResource;
use Laminas\Session\Container;
use Application\Adapter\Auth\UserAuthAdapter;

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

    public function __construct(TestRepositoryInterface $testRepository, CategoryRepositoryInterface $categoryRepository,
                ProviderRepositoryInterface $providerRepository, StoreRepositoryInterface $storeRepository,
                ProductRepositoryInterface $productRepository, FilteredProductRepositoryInterface $filteredProductRepository,
                BrandRepositoryInterface $brandRepository, ColorRepositoryInterface $colorRepository, SettingRepositoryInterface $settingRepository,
                CharacteristicRepositoryInterface $characteristicRepository,
                PriceRepositoryInterface $priceRepository, StockBalanceRepositoryInterface $stockBalanceRepository,
                HandbookRelatedProductRepositoryInterface $handBookProduct,
                $entityManager, $config, HtmlProviderService $htmlProvider, HtmlFormProviderService $htmlFormProvider, UserRepository $userRepository, AuthenticationService $authService,
                $productCharacteristicRepository)
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
        $userAuthAdapter = new UserAuthAdapter($this->userRepository);
        $result = $this->authService->authenticate($userAuthAdapter);
        $code = $result->getCode();
        if($code != \Application\Adapter\Auth\UserAuthResult::SUCCESS) {
            throw new \Exception('Unknown error in IndexController');
        }

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
}
