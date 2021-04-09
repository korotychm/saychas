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
use Application\Resource\StringResource;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
use Laminas\Http\Response;
use Laminas\Session\Container;

class AjaxController extends AbstractActionController
{
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

    public function __construct(TestRepositoryInterface $testRepository, CategoryRepositoryInterface $categoryRepository,
                ProviderRepositoryInterface $providerRepository, StoreRepositoryInterface $storeRepository,
                ProductRepositoryInterface $productRepository, FilteredProductRepositoryInterface $filteredProductRepository, BrandRepositoryInterface $brandRepository, 
                CharacteristicRepositoryInterface $characteristicRepository, PriceRepositoryInterface $priceRepository, StockBalanceRepositoryInterface $stockBalanceRepository, $entityManager, $config, HtmlProviderService $htmlProvider)
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
        $json=$post->value;
        try {
            $TMP = Json::decode($json);
        }
        catch(LaminasJsonRuntimeException $e){
           return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
        
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
    
    
    public function ajaxGetLegalStoreAction()
    {
        $post = $this->getRequest()->getPost();
        $json=$post->value;
        try {
            $TMP = Json::decode($json);
        }
        catch(LaminasJsonRuntimeException $e){
        exit($e->getMessage());          
// return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
        $ob=$TMP-> data;
        if (!$ob->house) return (StringResource::USER_ADDREES_ERROR_MESSAGE);
        
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $container->userAddress = $TMP -> value;
        
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
        $legalStore=Json::decode($result, true);
        foreach($legalStore as $store) {$sessionLegalStore[$store['store_id']]=$store['deliverySpeedInHours'];}
        
        $container->legalStore = $sessionLegalStore; //Json::decode($result, true);
        //exit(print_r($container->legalStore));
        exit ("200");
    }
    
    public function ajaxSetUserAddressAction()
    {
        $json["userAddress"] = $this->htmlProvider->writeUserAddress();
        $container = new Container(StringResource::SESSION_NAMESPACE);  
        $json["legalStore"] =  $container->legalStore;
        exit(Json::encode($json,JSON_UNESCAPED_UNICODE ));
    }
    
    public function setFilterForCategotyAction()
    {
        
        $post=$this->getRequest()->getPost();
        $categoty_id=$post->categoty_id;
        
        foreach ($post as $key=>$value)
            //echo "$key=>$value <br>"; exit();
            $fltrArray[$key]=$value;
        
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $filtrForCategory=$container->filtrForCategory;
        $filtrForCategory[$categoty_id]=$fltrArray; 
        $container->filtrForCategory = $filtrForCategory;
        exit (print_r($container->filtrForCategory));
        
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
          
            $providers = $this->providerRepository->findAvailableProviders([ 'order'=>'id ASC', 'limit'=>100, 'offset'=>0, 'sequence' => [1,2,3,4,5]]);
            //$providers = $this->providerRepository->findAll(['limit'=>100, 'offset' => 0, 'order' =>'id ASC']);
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
        //$categories = $this->categoryRepository->findAllCategories("", 0, $category_id);
        $categories = $this->categoryRepository->findAll(["id"=>$category_id]);
        $bread = $this->categoryRepository->findAllMatherCategories($category_id);
        $bread = $this->htmlProvider->breadCrumbs($bread);
         
     
        $categoryTree = $this->categoryRepository->findCategoryTree($category_id);
        $products = $this->productRepository->filterProductsByStores(['000000003', '000000004', '000000005', '000000001', '000000002']);
        $filteredProducts = $this->productRepository->filterProductsByCategories($products, $categoryTree);
        //  exit(print_r($filteredProducts));
        
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $addresForm = "". $this->htmlProvider->inputUserAddressForm(['seseionUserAddress'=>$container-> seseionUserAddress]);
        
        $returnProduct = $this->htmlProvider->productCard($filteredProducts);
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
    
    public function testReposAction()
    {
        $this->layout()->setTemplate('layout/mainpage');
        
        $stores = $this->storeRepository->findAll(['table'=>'store', 'sequance' => ['000000003', '000000004', '000000005'] ]);//, '000000001', '000000002'['000000003', '000000004', '000000005']
        $brands = $this->brandRepository->findAll(['table'=>'brand']);
        $characteristics = $this->characteristicRepository->findAll(['table'=>'characteristic']);
        $products = $this->productRepository->findAll(['table'=>'product', 'limit'=>100, 'offset'=>0, 'order'=>'id ASC']);
        $prices = $this->priceRepository->findAll(['table'=>'price']);
        $stockBalances = $this->stockBalanceRepository->findAll(['table'=>'stock_balance']);
        $filteredProducts = $this->filteredProductRepository->filterProductsByStores(['000000005', '000000004']);
        
        foreach ($stores as $store) {
            echo $store->getId().' '.$store->getTitle(). '<br/>';
        }
        echo '<hr/>';
        foreach ($brands as $brand) {
            echo $brand->getId().' '.$brand->getTitle(). '<br/>';
        }
        echo '<hr/>';
        foreach ($characteristics as $characteristic) {
            echo $characteristic->getId().' '.$characteristic->getTitle(). '<br/>';
        }
        echo '<hr/>';
        foreach ($products as $product) {
            echo $product->getId().' '.$product->getTitle(). '<br/>';
        }
        echo '<hr/>';
        foreach ($prices as $price) {
            echo $price->getPrice().' '.$price->getProductId().' '.$price->getStoreId(). '<br/>';
        }
        echo '<hr/>';
        foreach ($stockBalances as $stockBalance) {
            echo $stockBalance->getRest().' '.$stockBalance->getProductId().' '.$price->getStoreId(). '<br/>';
        }
        echo '<hr/>';
        foreach ($filteredProducts as $filteredProduct) {
            echo $filteredProduct->getId().' '.$filteredProduct->getTitle(). ' '. $filteredProduct->getProductId().' ' . $filteredProduct->getProductTitle(). ' '. $filteredProduct->getRest(). '<br/>';
        }
        exit;
        
    }
    
    
}
