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
use Application\Model\RepositoryInterface\BrandRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Service\HtmlProviderService;
use Application\Resource\StringResource;
//use Doctrine\ORM\Mapping as ORM;
use Application\Entity\Post;
//use Psr\Http\Message\ResponseInterface;
use \InvalidArgumentException;
use Laminas\Http\Response;
//use Laminas\Session;
use Laminas\Session\Container;

use Application\Model\Entity\Characteristic;

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
    private $brandRepository;
    private $characteristicRepository;
    private $entityManager;
    private $config;
    private $htmlProvider;

    public function __construct(TestRepositoryInterface $testRepository, CategoryRepositoryInterface $categoryRepository,
                ProviderRepositoryInterface $providerRepository, StoreRepositoryInterface $storeRepository,
                ProductRepositoryInterface $productRepository, BrandRepositoryInterface $brandRepository, 
                CharacteristicRepositoryInterface $characteristicRepository, $entityManager, $config, HtmlProviderService $htmlProvider)
    {
        $this->testRepository = $testRepository;
        $this->categoryRepository = $categoryRepository;
        $this->providerRepository = $providerRepository;
        $this->storeRepository = $storeRepository;
        $this->productRepository = $productRepository;
        $this->brandRepository = $brandRepository;
        $this->characteristicRepository = $characteristicRepository;
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->htmlProvider = $htmlProvider;
    }

    public function onDispatch(MvcEvent $e) 
    {
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);
        
        $servicemanager = $e->getApplication()->getServiceManager();

//        $this->categoryRepository = $servicemanager->get(CategoryRepositoryInterface::class);
        
//        $category = $this->categoryRepository->findCategory(29);
//        
//        $e->getApplication()->getMvcEvent()->getViewModel()->setVariable('category', $category );

        // Return the response
        $this->layout()->setVariables([
            'headerText' => $this->htmlProvider->testHtml(),
            'footerText' => 'banzaii',
        ]);
        $this->layout()->setTemplate('layout/mainpage');
        return $response;
        
    }
    
    public function indexAction()
    {
        $container = new Container(StringResource::SESSION_NAMESPACE);

//        $adapter = new Adapter([
//            'driver'   => 'Pdo_Mysql',
//            'database' => 'saychas_z',
//            'username' => 'saychas_z',
//            'password' => 'saychas_z',
//        ]);
        
//        $sql    = new Sql($adapter);
//        $select = $sql->select();
//        $select->from('test');
//        $select->where(['id' => 2]);
//
//        $selectString = $sql->buildSqlString($select);
//        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return new ViewModel([
            'fooItem' => $container->item
//            'tests' => $this->testRepository->findAllTests(),
//            'first' => $this->testRepository->findTest(4),
//            'provider' => $this->providerRepository->find(['id' => '00004']),
//            'product' => $this->productRepository->find(['id' => '000000000004']),
//            'brand' => $this->brandRepository->find(['id' => '000002']),
//            'store' => $this->storeRepository->find(['id' => '000000003']),
//            'category' => $this->categoryRepository->findCategory(['id' => '29']),
//            'providers' => $this->providerRepository->findAll(),
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
    
    public function ajaxToWebAction()
    {
        $post = $this->getRequest()->getPost();
        
        $url = $this->config['parameters']['1c_request_links']['get_product'];
        $params = array('name' => 'value');
        $result = file_get_contents(
            $url, 
            false, 
            stream_context_create([
                'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($params)]
            ])
         );
        
        $return.="<pre>";
        $return.= date("r")."\n" ;
        // if($result) print_r(json_decode($result));
        $return.="{$post -> name} => {$post -> value}";
        $return.="</pre>";
        exit ($return);
        
        return new JsonModel([]);

    }
    
    public function ajaxGetStoreAction()
    {
        //$id=$this->params()->fromRoute('id', '');
        $post = $this->getRequest()->getPost();
        
        $url = $this->config['parameters']['1c_request_links']['get_store'];

        $result = file_get_contents(
            $url,
            false,
            stream_context_create(array(
                'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => $post->value
                )
                ))
        );
        
        
               
        
        
        $r = print_r(json_decode($result,true),true);
//        $return.="<pre>";
//        $return.= date("r")."\n";
//        if($result) {
//            $return.=print_r(json_decode($result,true),true);
//        }
//        $return.="</pre>";
//        exit ($return);
//        $response = Response::fromString(<<<EOS
//        HTTP/1.0 200 OK
//        HeaderField1: header-field-value
//        HeaderField2: header-field-value2
//
//        <html>
//        <body>
//            <pre>
//                $r
//            </pre>
//        </body>
//        </html>
//        EOS);
        $response = new Response();
        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->getHeaders()->addHeaders([
            'HeaderField1' => 'header-field-value',
            'HeaderField2' => 'header-field-value2',
        ]);
        //$r = $this->htmlProvider->testHtml();
        $response->setContent(<<<EOS
        <html>
        <body>
                <pre>
            $r
                </pre>
        </body>
        </html>
        EOS);
        return $response;
    }
    
    public function ajaxAction($params = array('name' => 'value')){   
        $id=$this->params()->fromRoute('id', '');
        $post = $this->getRequest()->getPost();
        $param=array(1,2,3,4,5);   
        
        if ($id=="toweb"){
            $url="http://SRV02:8000/SC/hs/site/get_product";
            $params = array('name' => 'value');
            $result = file_get_contents(
                $url, 
                false, 
                stream_context_create(array(
                    'http' => array(
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => http_build_query($params))	
                ))
             );
            $return.="<pre>";
            $return.= date("r")."\n" ;
            // if($result) print_r(json_decode($result));
            $return.="{$post -> name} => {$post -> value}";
            $return.="</pre>";
            exit ($return);
        }
         if ($id=="getstore"){
             
            //.print_r($post,true));  
            $url="http://SRV02:8000/SC/hs/site/get_product";
            
            $result = file_get_contents(
                $url,
                false,
                stream_context_create(array(
                    'http' => array(
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/json',
                    'content' => $post->value
                    )
                ))
             );
            $return.="<pre>";
            $return.= date("r")."\n";
            if($result) $return.=print_r(json_decode($result,true),true);
            $return.="</pre>";
            exit ($return);
        }
        if ($id=="getproviders"){                
          
            $providers = $this->providerRepository->findAvailableProviders($param);
            $providers = $this->providerRepository->findAll();
              //if (!$providers )    exit(date("r")."<h3>Объект provider не&nbsp;получен</h3> <a href=# rel='666' class=provider-list  >Запросить тестовые магазины </a> <hr/>"); 
            $return.=date("r");	
            $return.="<ul>";
            foreach ($providers as $row){
                $return.="<li><a href=# rel='{$row -> getId()}' class=provider-list >{$row -> getTitle()}</a></li>";
              
            }
            $return.="</ul>";
            exit ($return);
        }	
        if ($id=="getshops"){
            $stores = $this->storeRepository->findStoresByProviderIdAndExtraCondition($post -> provider, $param) ;
            $return.=date("r")."<br>";	
            $return.="id постащика: {$post -> provider}<hr>" ;
            if (! $stores ) exit($return."<h3>Объект store не&nbsp;получен</h3> <a href=# rel='2' class=shop-list  >Запросить тестовые товары </a>"); 
            $return.="<ul>";
            foreach ( $stores as $row)
                $return.="<li><a href=#product rel='{$row -> getId()}' class=shop-list title='{$row->getAddress()} \r\n  {$row->getGeox()} , {$row -> getGeoy()} ' >"
               ."{$row -> getTitle()}</a>"
               ."</li>";
            $return.="</ul>";
            exit ($return);
        }	
        if ($id=="getproducts"){
            $products = $this->productRepository->findProductsByProviderIdAndExtraCondition($post -> shop, $param );
            $str = StringResource::PRODUCT_FAILURE_MESSAGE;
            
            $return.=date("r")."<br>";	
            $return.="id магазина: {$post -> shop}<hr>" ;

            foreach ($products as $row){
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
                ];
                /*                
                provider_id
                category_id
                price
                rest
                url_http
                brand_title
                store_title
                store_address
                store_description
                param_value_list
                param_variable_list
                title
                description
                vendor_code
                howHour
                 * 
                 * 
                 */
               $return.= $this->htmlProvider->productCard($productCardParam);
            }/**/
            
            exit ($return); 
        }	
        header('HTTP/1.0 404 Not Found');
        exit(); 
    }

    public function providerAction()
    {
        $id=$this->params()->fromRoute('id', '');
        $this->layout()->setTemplate('layout/mainpage');
        $categories = $this->categoryRepository->findAllCategories("", 0, $id);
        $providers = $this->providerRepository->findAll();
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
        $products = $this->productRepository->filterProductsByStores(['000000003', '000000008', '000000009']);
        $filteredProducts = $this->productRepository->filterProductsByCategories($products, $categoryTree);
        
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $addresForm = "". $this->htmlProvider->inputUserAddressForm(['seseionUserAddress'=>$container-> seseionUserAddress]);
        
        
        foreach ($filteredProducts as $row){
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
                ];
        
            $returnProduct.= $this->htmlProvider->productCard($productCardParam);
        }
        
        
        
        
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
        
        $products = $this->productRepository->filterProductsByStores(['000000003', '000000008', '000000009']);
        
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
        $this->layout()->setTemplate('layout/mainpage');
        
        $characteristic = $this->characteristicRepository->find([ 'id' => '000000002']);
        
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

        return $view;
    }
    
    
}
