<?php
/**
 * changed
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
use Application\Model\RepositoryInterface\ProviderRelatedStoreRepositoryInterface;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;
use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;
use Application\Model\RepositoryInterface\BrandRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Application\Model\Repository\UserRepository;
use Application\Service\HtmlProviderService;
use Application\Service\HtmlFormProviderService;
use Application\Resource\StringResource;
use Laminas\Authentication\AuthenticationService;

use Laminas\Authentication\Adapter\DbTable\CredentialTreatmentAdapter as AuthAdapter;

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

class MyTestController extends AbstractActionController
{
    /**
     * @var TestRepositoryInterface
     */
    private $testRepository;
    private $categoryRepository;
    private $providerRepository;
    private $storeRepository;
    private $providerRelatedStoreRepository;
    private $productRepository;
    private $filteredProductRepository;
    private $brandRepository;
    private $characteristicRepository;
    private $priceRepository;
    private $stockBalanceRepository;
    private $handBookRelatedProductRepository;
    private $userRepository;
    private $entityManager;
    private $config;
    private $htmlProvider;
    private $htmlFormProvider;
    private $authService;

    public function __construct(TestRepositoryInterface $testRepository, CategoryRepositoryInterface $categoryRepository,
                ProviderRepositoryInterface $providerRepository, StoreRepositoryInterface $storeRepository,
                ProviderRelatedStoreRepositoryInterface $providerRelatedStoreRepository,
                ProductRepositoryInterface $productRepository, FilteredProductRepositoryInterface $filteredProductRepository, BrandRepositoryInterface $brandRepository, 
                CharacteristicRepositoryInterface $characteristicRepository,
                PriceRepositoryInterface $priceRepository, StockBalanceRepositoryInterface $stockBalanceRepository,
                HandbookRelatedProductRepositoryInterface $handBookProduct, UserRepository $userRepository,
            $entityManager, $config, HtmlProviderService $htmlProvider, HtmlFormProviderService $htmlFormProvider, $authService)
    {
        $this->testRepository = $testRepository;
        $this->categoryRepository = $categoryRepository;
        $this->providerRepository = $providerRepository;
        $this->storeRepository = $storeRepository;
        $this->providerRelatedStoreRepository = $providerRelatedStoreRepository;
        $this->productRepository = $productRepository;
        $this->filteredProductRepository = $filteredProductRepository;
        $this->brandRepository = $brandRepository;
        $this->characteristicRepository = $characteristicRepository;
        $this->priceRepository = $priceRepository;
        $this->stockBalanceRepository = $stockBalanceRepository;
        $this->handBookRelatedProductRepository = $handBookProduct;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->htmlProvider = $htmlProvider;
        $this->htmlFormProvider = $htmlFormProvider;
        $this->authService = $authService;
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
    
    public function testReposAction()
    {
        $adapter = new \Laminas\Db\Adapter\Adapter([
            'driver'   => 'Pdo_Mysql',
            'database' => 'saychas_z',
            'username' => 'saychas_z',
            'password' => 'saychas_z',
        ]);
        
        
        $authAdapter = new AuthAdapter($adapter);

        $authAdapter
            ->setTableName('user')
            ->setIdentityColumn('name')
            ->setCredentialColumn('email');

        $authAdapter
            ->setIdentity('my_username')
            ->setCredential('my_password');
        
        $result = $authAdapter->authenticate();

        $auth = new AuthenticationService();
        
        $result1 = $auth->authenticate($authAdapter);
        
        echo $result1->getIdentity();
        
        
//        if ($user = $this->identity()) {
//            echo 'Logged in as ' . $this->escapeHtml($user->getUsername());
//        } else {
//            echo 'Not logged in';
//        }
//        exit;
        
//        print_r($result->getIdentity());
//        exit;
        
//        if ($user = $this->identity()) {
//            // someone is logged !
//            print_r('banzaii');
//        } else {
//            // not logged in
//            print_r('vonzaii');
//        }
    
        $this->layout()->setTemplate('layout/mainpage');
        $handBookRelatedProducts = $this->handBookRelatedProductRepository->findAll(['where' => $this->packParams(['filter' => ['000000003', '000000014', '1b53a86f9d8c43c09ba1a7687f76685c', '919a484078a309202207bcd5eafefb97', '2ed1f50a2956c78164bdf967ef47c928', '5b4813eb4a21706f492ae4ee2716a7f9'] ]) ]);
//        $handBookRelatedProducts = $this->handBookRelatedProductRepository->findAll([]);
        echo '<table style="font-size: 10pt">';
        echo '<tr><th>Product id</th><th>ParamValueList</th><th>Product brand_id</th><th>ProductBrand title</th><th>ProductPrice<br/>product_id</th><th>ProductPrice<br/>price</th><th>Product title</th><th>ProductPrice<br/>provider_id</th></tr>';
        
        foreach($handBookRelatedProducts as $prod) {
            echo '<tr>';
//            echo '<td>'.$prod->getId().'</td><td>'. implode('<br/>', explode(',',  $prod->getParamValueList())).'</td><td>'.$prod->getBrandId().'</td><td>'.$prod->getBrand()->title.'</td><td>'. $prod->getPrice()->getProductId() . '</td><td>' . $prod->getPrice()->getPrice() . '</td><td>'.$prod->title.'</td><td>'.$prod->getPrice()->getProviderId().'</td>';
            $provider = $prod->getProvider();
            $strs = $provider->getStores();
            
            echo '<tr colspan="6" align="center"><td>';
            foreach ($strs as $st) {
                echo $provider->getTitle().' >>> '. $st->getProviderId().' '. $st->getTitle().'<br/>';
            }
            echo '</td></tr>';
            
            echo '<td>'.$provider->getId().' '.$provider->getTitle().'</td><td>'. implode('<br/>', explode(',',  $prod->getParamValueList())).'</td><td>'.$prod->getBrandId().'</td><td>'.$prod->getBrand()->title.'</td><td>'. $prod->getPrice()->getProductId() . '</td><td>' . $prod->getPrice()->getPrice() . '</td><td>'.$prod->title.'</td><td>'.$prod->getPrice()->getProviderId().'</td>';
            echo '</tr>';
//            echo '<tr colspan="6" align="center"><td>';
//            $images = $prod->getProductImages();
//            foreach ($images as $image) {
//                echo $image->getProductId().' '. $image->getHttpUrl().'<br/>';
//            }
//            echo '</td></tr>';
            
        }
        echo '</table>';
        $stores = $this->storeRepository->findAll(['sequence' => ['000000003', '000000004', '000000005'] ]);//, '000000001', '000000002'['000000003', '000000004', '000000005']
        $providerRelatedStoreRepository = $this->providerRelatedStoreRepository->findAll(['sequence' => ['000000003', '000000004', '000000005']]);
        $brands = $this->brandRepository->findAll([]);
        $characteristics = $this->characteristicRepository->findAll([]);
        $products = $this->productRepository->findAll(['limit'=>100, 'offset'=>0, 'order'=>'id ASC', 'store_filter' => ['000000003', '000000004', '000000005', '000000001', '000000002'] ]);
        $prices = $this->priceRepository->findAll(['table'=>'price']);
        $stockBalances = $this->stockBalanceRepository->findAll(['table'=>'stock_balance']);
        $filteredProducts = $this->filteredProductRepository->findAll(['order'=>'id ASC', 'limit'=>100, 'offset'=>0, 'sequence'=>['000000005', '000000004']]);//filterProductsByStores(['000000005', '000000004']);
        $filteredProducts2 = $this->productRepository->filterProductsByStores(['000000003', '000000004', '000000005', '000000001', '000000002']);// findAll(['limit'=>100, 'offset'=>0, 'order'=>'id ASC']);
        //$providers = $this->providerRepository->findAvailableProviders(['000000003', '000000004', '000000005'], 'id ASC', 100, 0);
        
        $providers = $this->providerRepository->findAvailableProviders([ 'order'=>'id ASC', 'limit'=>100, 'offset'=>0, 'sequence'=>['000000003', '000000004', '000000005'] ]);
        $providers2 = $this->providerRepository->findAll(['order'=>'id ASC', 'limit'=>100, 'offset'=>0, 'sequence'=>['00003'], 'where'=>[ 'id' => ['00003', '00004']] ]);
        
        echo '==================== ProviderRelatedStoreRepository ==========================<br/>';
        foreach($providerRelatedStoreRepository as $sp){
            echo '<pre>';
            $provider = $sp->getProvider();
//            print_r($sp);
            echo $sp->id.' '. $sp->getProviderId().' '. $sp->getTitle().' '.$sp->getDescription().' '. $sp->getAddress(). ' '. $sp->getGeox().' '.$sp->getGeoy(). $sp->getIcon().' '. $provider->getId().' '.$provider->getTitle().' '.$provider->getDescription().' '.$provider->getIcon().'<br/>';
            echo '</pre>';
        }
        echo '==================== End ================================================<br/>';
        
        
        $users = $this->userRepository->findAll([]);
        
//        foreach($users as $user) {
//            echo '<pre>';
//            print_r($user);
//            echo '</pre>';
//        }
//        exit;
        
        $form = $this->htmlFormProvider->testForm();
        echo $form.'<br/>';

        echo '---<br/>Store, function: findAll <br/>';
        foreach ($stores as $store) {
            echo $store->getId().' store title = '.$store->getTitle() . ' property title: ' . $store->title . ' id: ' .$store->id . '<br/>';
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
//            $product->vendor_code = $product->vendor_code . ' banzaii';
//            echo '<pre>';
//            print_r($product);
//            echo '</pre>';
//            exit;
            echo $product->getId().' '.$product->getTitle(). ' property: ' . $product->title . ' vendor_code: '. $product->vendor_code . '<br/>';
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
        echo '---<br/>Filtered Products2, function: filterProductsByStores <hr/>';
        foreach ($filteredProducts2 as $product2) {
            echo $product2->getId().' '.$product2->getTitle(). ' price = '. $product2->getPrice(). $product2->getStoreTitle(). ' '. $product2->getStoreId(). ' '.$product2->getProviderId(). '<br/>';
        }
        echo '---<br/>Providers, function: findAvailableProviders <hr/>';
        foreach ($providers as $provider) {
            echo $provider->getId().' '.$provider->getTitle().' ' . $provider->getDescription(). ' property title: ' .$provider->title . '<br/>';
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
