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
use Application\Resource\StringResource;

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

    public function __construct(TestRepositoryInterface $testRepository, CategoryRepositoryInterface $categoryRepository,
                ProviderRepositoryInterface $providerRepository, StoreRepositoryInterface $storeRepository,
                ProductRepositoryInterface $productRepository)
    {
        $this->testRepository = $testRepository;
        $this->categoryRepository = $categoryRepository;
        $this->providerRepository = $providerRepository;
        $this->storeRepository = $storeRepository;
        $this->productRepository = $productRepository;
    }

    public function onDispatch(MvcEvent $e) 
    {
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);
        
        $servicemanager = $e->getApplication()->getServiceManager();

        $this->categoryRepository = $servicemanager->get(CategoryRepositoryInterface::class);
        
        //$category = $this->categoryRepository->findCategory(29);
        
        $e->getApplication()->getMvcEvent()->getViewModel()->setVariable('category', $category );

        // Return the response
        return $response;
    }
    
    public function indexAction()
    {
        
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
            'tests' => $this->testRepository->findAllTests(),
//            'first' => $this->testRepository->findTest(4),
            //'provider' => $this->providerRepository->find(4),
            'providers' => $this->providerRepository->findAll(),
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
    
    public function ajaxAction(){   
        $id=$this->params()->fromRoute('id', '');
        $post = $this->getRequest()->getPost();
        $paramp=array();   
                $paramp[]=1;     
                $paramp[]=2;
                $paramp[]=3;
                $paramp[]=4;
                $paramp[]=5;
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
        if ($id=="getproviders"){                
            $providers = $this->providerRepository->findAll();
          //if (!$providers )    exit(date("r")."<h3>Объект provider не&nbsp;получен</h3> <a href=# rel='666' class=provider-list  >Запросить тестовые магазины </a> <hr/>"); 
            $return.=date("r");	
            $return.="<ul>";
            foreach ($providers as $row)
                $return.="<li><a href=# rel='{$row -> getId()}' class=provider-list >{$row -> getTitle()}</a></li>";
            $return.="</ul>";
            exit ($return);
        }	
        if ($id=="getshops"){
            //sleep(1);
            $stores = $this->storeRepository->findStoresByProviderIdAndExtraCondition($post -> provider, $paramp) ;
            $return.=date("r")."<br>";	
            $return.="id постащика: {$post -> provider}<hr>" ;
            if (! $stores ) exit($return."<h3>Объект store не&nbsp;получен</h3> <a href=# rel='2' class=shop-list  >Запросить тестовые товары </a>"); 
            $return.="<ul>";
            foreach ( $stores as $row)
               // exit(print_r($row));
                $return.="<li><a href=# rel='{$row -> getId()}' class=shop-list title='{$row->getAddress()} \r\n  {$row->getGeox()} , {$row -> getGeoy()} ' >"
               ."{$row -> getTitle()}</a><br>"
               . "{$row->getAddress()} \r\n  {$row->getGeox()} , {$row -> getGeoy()} "
               ."</li>";
            $return.="</ul>";
            
                       
            exit ($return);
        }	
        if ($id=="getproducts"){
            //$products = $this->productRepository->findAll();
            
            $products = $this->productRepository->findProductsByProviderIdAndExtraCondition($post -> shop, $paramp );
            //sleep(1);
            $str = StringResource::PRODUCT_FAILURE_MESSAGE;
            
            //exit($str);
            if (!count($products)) exit(date("r")."<h3>для магазина id:{$post -> shop} товаров не найдено</h3> "); 
            $return.=date("r")."<br>";	
            $return.="id магазина: {$post -> shop}<hr>" ;
            $return.="<ul>";
            foreach ($products as $row)
                $return.="<li><a href=# rel='{$row -> getId()}' >{$row -> getTitle()}</a></li>";
            $return.="</ul>";
            exit ($return);
        }	
        header('HTTP/1.0 404 Not Found');
        exit(); 
    }
    public function providerAction()
    {
        $this->layout()->setTemplate('layout/provider');
        $this->providerRepository->findAll();
        return new ViewModel([
            "providers" => $this->providerRepository->findAll(),
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
}
