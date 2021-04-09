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
//use Laminas\Db\Adapter\Adapter;
//use Laminas\Db\Sql\Sql;
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
//use Laminas\Json\Json;
//use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
//use Doctrine\ORM\Mapping as ORM;
use Application\Entity\Post;
//use Psr\Http\Message\ResponseInterface;
use \InvalidArgumentException;
//use Laminas\Http\Response;
//use Laminas\Session;
use Laminas\Session\Container;
use Application\Model\Test2;
use Application\Model\Track;
use Application\Model\Entity\Characteristic;
use \ReflectionClass;

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

    public function catalogAction()
    {
        $category_id=$this->params()->fromRoute('id', '');

        $this->layout()->setTemplate('layout/mainpage');
        $categories = $this->categoryRepository->findAllCategories("", 0, $category_id);
        $bread = $this->categoryRepository->findAllMatherCategories($category_id);
        $bread = $this->htmlProvider->breadCrumbs($bread);
         
     
        $categoryTree = $this->categoryRepository->findCategoryTree($category_id);
        $products = $this->productRepository->filterProductsByStores2(['000000003', '000000004', '000000005', '000000001', '000000002']);
        $filteredProducts = $this->productRepository->filterProductsByCategories($products, $categoryTree);
        //  exit(print_r($filteredProducts));
        
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $addresForm = "". $this->htmlProvider->inputUserAddressForm(['seseionUserAddress'=>$container-> seseionUserAddress]);
        
        
        /*foreach ($filteredProducts as $row){
              $productCardParam = [
                    'price'=>$row->getPrice(),
                    'title'=>$row->getTitle(),
                    'img'=>$row->getUrlHttp(),
                    'id'=>$row->getId(),
                    'rest'=>$row->getRest(),
                    'articul'=>$row->getVendorCode(),
                    'brand'=>$row->getBrandTitle(),
                    'description'=>$row->getDescription(),
                    'param_value'=>$row->getParamValueList(),
                    'param_value'=>$row->getParamValueList(),
                    'store'=>$row->getStoreTitle()." (id:{$row->getStoreId()})",
                    'store_id'=>$row->getStoreId(),
                    //'store_address'=>$row->storeAddress(),
                ];
        
           
            
        }*/
         $returnProduct.= $this->htmlProvider->productCard($filteredProducts);
        
        
        
        try {
            $categoryTitle = $this->categoryRepository->findCategory(['id' => $category_id])->getTitle();
        }
        catch (\Exception $e) {
            $categoryTitle = "&larr;Выбери категорию товаров  ";
        }     
            
        
        return new ViewModel([
        
            "catalog" => $categories,
            "title" => $categoryTitle,
            "id" => $category_id,
            "bread"=> $bread,
            'priducts'=> $returnProduct,
            'addressform'=> $addresForm."", //print_r($bread,true),
        ]);

    }
    
    public function showStoreAction()
    {
        //$this->storeRepository->findProductsByProviderIdAndExtraCondition(1, [1, 2]);
        $stores = $this->storeRepository->findStoresByProviderIdAndExtraCondition(1, [1,2, 3]);
        $stores = $this->storeRepository->findAll(['000000001', 2, 3]);
        
        foreach($stores as $store) {
            echo '<pre>';
            print_r($store);
            echo '</pre>';
        }
        
        exit;
        
    }
    
    public function showProductAction()
    {
        $products = $this->productRepository->findProductsByProviderIdAndExtraCondition(1, [1, 2]);
        
        foreach ($products as $product) {
            echo $product->getId() . ' ' . $product->getTitle() . '<br/>';
        }
        exit;
    }
    
    public function addNewPostAction()
    {
        $id=$this->params()->fromRoute('id', '1');
        if(empty($id)) {
            throw new InvalidArgumentException('id must not be null');
        }
        
        // Создаем новую сущность Post.
        $post = new \Application\Entity\Post();
        $post->setId($id);
        $post->setTitle('Top 10+ Books about Zend Framework 3');
        $post->setContent('Post body goes here');
        $post->setStatus(Post::STATUS_PUBLISHED);
        $currentDate = date('Y-m-d H:i:s');
        $post->setDateCreated($currentDate);
        
//        echo '<pre>';
//        print_r($post);
//        echo '</pre>';
//        exit;

        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($post);
//        echo '<pre>';
//        print_r($post);
//        echo '</pre>';
//        exit;

        // Применяем изменения к БД.
        $this->entityManager->flush();
        
        return new \Laminas\Diactoros\Response\JsonResponse(['banzaii' => 'vonzaii'], 401);
        
        echo "id = ". $id;
        exit;
    }
    
    public function testingAction()
    {
        $characteristic = new Characteristic();
        $characteristic->setId('1111')->setCategoryId('000000009')->setTitle('Title1')->setType(1)->setGroup(2);
        echo '<pre>';
        print_r($characteristic);
        echo '</pre>';
        exit;
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $container->item = 'foo';

        $id=$this->params()->fromRoute('id', '0');

        $category_id=$this->params()->fromRoute('id', '0');

        $categoryTree = $this->categoryRepository->findCategoryTree($category_id);
        
        $products = $this->productRepository->filterProductsByStores(['000000003', '000000004', '000000005', '000000001', '000000002']);
        
        $filteredProducts = $this->productRepository->filterProductsByCategories($products, $categoryTree);
        
        foreach($filteredProducts as $r) {
            echo '<pre>';
            print_r($r);
            echo '</pre>';
        }
        exit;
    }
    
    public function helloWorldAction()
    {
        //https://docs.laminas.dev/laminas-hydrator/v3/strategies/collection/
        
        $products = $this->productRepository->filterProductsByStores(['000000005', '000000004']);

//        echo '<pre>';
//        print_r($products);
//        echo '</pre>';
//        exit;
        
        
        $hydrator = new \Laminas\Hydrator\ReflectionHydrator();
        $hydrator->addStrategy(
            'tracks',
            new \Laminas\Hydrator\Strategy\CollectionStrategy(
                new \Laminas\Hydrator\ReflectionHydrator(),
                Track::class
            )
        );
//        $t = [
//            'artist' => 'Shartist',
//            'title'  => 'Banzaii',
//            'tracks' => [
//                [
//                    'title'    => 'Vovan skache',
//                    'duration' => '4:46',
//                ],
//                [
//                    'title'    => 'Vovan nie skache',
//                    'duration' => '5:32',
//                ],
//                [
//                    'title'    => 'Vovan doprygalsa',
//                    'duration' => '7:38',
//                ],
//                // …
//            ],
//        ];
        
        $test2 = new Test2();
//        
//        $arr = [ [ 'artist' => 'Shmartist', 'ttitle'  => 'Banzaii', 'title'    => 'Vovan skache', 'duration' => '4:46', ],
//                 [ 'artist' => 'Shmartist', 'ttitle'  => 'Banzaii', 'title'    => 'Vovan nie skache', 'duration' => '5:32', ],
//                 [ 'artist' => 'Shmartist', 'ttitle'  => 'Banzaii', 'title'    => 'Vovan doprygalsa', 'duration' => '7:38', ],
//                    // …
//            ];
//        $s = array_slice($arr,0, 2);
//        //print_r($s);
//        
//        $result = [];
//        $result['artist'] = array_slice($arr[0], 0, 1)['artist'];
//        $result['ttitle'] = array_slice($arr[0], 0, 2)['ttitle'];
//        $result['tracks'] = [];
//        foreach($arr as $a) {
//            $item = array_slice($a, 2);
//            $result['tracks'][] = $item;
//        }
//        echo '<pre>';
//        print_r($t);
//        echo '</pre>';
//        echo '<pre>';
//        print_r($result);
//        echo '</pre>';
//        exit;
//        foreach ($arr as $item) {
//            echo '<pre>';
//            print_r($item) ;
//            //print_r(array_column($item, 'artist') ) ;
//            echo '</pre>';
//        }
//        exit;
        
        $hydrator->hydrate(
            [
                'artist' => 'Shmartist',
                'title'  => 'Banzaii',
                'tracks' => [
                    [
                        'title'    => 'Vovan skache',
                        'duration' => '4:46',
                    ],
                    [
                        'title'    => 'Vovan nie skache',
                        'duration' => '5:32',
                    ],
                    [
                        'title'    => 'Vovan doprygalsa',
                        'duration' => '7:38',
                    ],
                    // …
                ],
            ],
            $test2
        );

        echo $test2->getTitle().' : '; // "Let's Dance"
        echo $test2->getArtist().'<br/>'; // 'David Bowie'
//        echo $test2->getTracks()[1]->getTitle(); // 'China Girl'
//        echo $test2->getTracks()[1]->getDuration(); // '5:32'
        
        foreach($test2->getTracks() as $track) {
            echo $track->getTitle().' '.$track->getDuration().'<br/>';
        }

        echo '<hr/>';
        $characteristic = $this->characteristicRepository->find([ 'id' => '000000001']);
        $this->productRepository->filterProductsByStores(['000000001','000000004','000000003','000000005','000000002']);
        
        echo '<pre>';
        print_r($characteristic);
        echo '</pre>';
        exit;
        
        
//        $this->layout()->setVariables([
//            'headerText' => $this->htmlProvider->testHtml(),
//            'footerText' => 'banzaii',
//        ]);

//        $view = new ViewModel([
//            'message' => 'Hello world',
//        ]);
//
//        // Capture to the layout view's "article" variable
//        $view->setCaptureTo('article');

//        return $view;
    }
    
    public function testReposAction()
    {
        $this->layout()->setTemplate('layout/mainpage');
        
        $stores = $this->storeRepository->findAll(['sequence' => ['000000003', '000000004', '000000005'] ]);//, '000000001', '000000002'['000000003', '000000004', '000000005']
        $brands = $this->brandRepository->findAll([]);
        $characteristics = $this->characteristicRepository->findAll([]);
        $products = $this->productRepository->findAll(['limit'=>100, 'offset'=>0, 'order'=>'id ASC']);
        $prices = $this->priceRepository->findAll(['table'=>'price']);
        $stockBalances = $this->stockBalanceRepository->findAll(['table'=>'stock_balance']);
        $filteredProducts = $this->filteredProductRepository->findAll(['order'=>'id ASC', 'limit'=>100, 'offset'=>0, 'sequence'=>['000000005', '000000004']]);//filterProductsByStores(['000000005', '000000004']);
        //$providers = $this->providerRepository->findAvailableProviders(['000000003', '000000004', '000000005'], 'id ASC', 100, 0);
        
        $providers = $this->providerRepository->findAvailableProviders([ 'order'=>'id ASC', 'limit'=>100, 'offset'=>0, 'sequence'=>['000000003', '000000004', '000000005'] ]);
        $providers2 = $this->providerRepository->findAll(['order'=>'id ASC', 'limit'=>100, 'offset'=>0, 'sequence'=>['00003'], 'where'=>[ 'id' => ['00003', '00004']] ]);
        
        $form = $this->htmlFormProvider->testForm();
        echo $form.'<br/>';

        echo '---<br/>Store, function: findAll <br/>';
        foreach ($stores as $store) {
            echo $store->getId().' '.$store->getTitle(). '<br/>';
        }
        echo '---<br/>Brand, function: findAll <hr/>';
        foreach ($brands as $brand) {
            echo $brand->getId().' '.$brand->getTitle(). '<br/>';
        }
        echo '---<br/>Characteristic, function: findAll <hr/>';
        foreach ($characteristics as $characteristic) {
            echo $characteristic->getId().' '.$characteristic->getTitle(). '<br/>';
        }
        echo '---<br/>Product, function: findAll <hr/>';
        foreach ($products as $product) {
            echo $product->getId().' '.$product->getTitle(). '<br/>';
        }
        echo '---<br/>Price, function: findAll <hr/>';
        foreach ($prices as $price) {
            echo $price->getPrice().' '.$price->getProductId().' '.$price->getStoreId(). '<br/>';
        }
        echo '---<br/>Stock Balance, function: findAll <hr/>';
        foreach ($stockBalances as $stockBalance) {
            echo $stockBalance->getRest().' '.$stockBalance->getProductId().' '.$stockBalance->getStoreId(). '<br/>';//.$price->getStoreId(). '<br/>';
        }
        echo '---<br/>Filtered Products, function: findAll <hr/>';
        foreach ($filteredProducts as $filteredProduct) {
            echo $filteredProduct->getId().' '.$filteredProduct->getTitle(). ' '. $filteredProduct->getProductId().' ' . $filteredProduct->getProductTitle(). ' '. $filteredProduct->getRest(). '<br/>';
        }
        echo '---<br/>Providers, function: findAvailableProviders <hr/>';
        foreach ($providers as $provider) {
            echo $provider->getId().' '.$provider->getTitle().' ' . $provider->getDescription().'<br/>';
        }
        echo '---<br/>Providers2, function: findAll <hr/>';
        foreach ($providers2 as $provider2) {
            echo $provider2->getId().' '.$provider2->getTitle().' ' . $provider2->getDescription().'<br/>';
        }

        echo "<br/>================ Category Tree ======================<br/>";

        $tree = $this->categoryRepository->findAll(['id' => 0]);

//        $strategy = new \Laminas\Hydrator\Strategy\HydratorStrategy(
//            new \Laminas\Hydrator\ReflectionHydrator(),
//            \Application\Model\Entity\Categ::class
//        );
        
//        $hydrated = $strategy->hydrate([
//            'children' => $strategy->hydrate($tree[0]),
//        ]);
//        $i = 0;
//        $builtTree = new \Application\Model\Entity\Categ();
//        while(isset($tree[$i]['children'])) {
//            $children = $tree[$i]['children'];
//            $builtTree->Id = $tree[$i]['id'];
//            $builtTree->Title = $tree[$i]['title'];
//            $builtTree->ParentId = $tree[$i]['parent_id'];
//            $builtTree->Children = $strategy->hydrate(['children' =>$children]);
//            $i += 1;
//        }
//
        //$categ = new \Application\Model\Entity\Categ();
//        $hydrator = new \Laminas\Hydrator\ReflectionHydrator();
//        $hydrator->addStrategy(
//            'children',
//            new \Laminas\Hydrator\Strategy\CollectionStrategy(
//                new \Laminas\Hydrator\ReflectionHydrator(),
//                \Application\Model\Entity\Categ::class
//            ),
//        );
//
//        $categs = $hydrator->hydrate($tree, (new \ReflectionClass(\Application\Model\Entity\Categ::class))->newInstanceWithoutConstructor());
        
        echo '<pre>';
        print_r($tree);
        echo '</pre>';

        exit;
        
    }
    
    
}
