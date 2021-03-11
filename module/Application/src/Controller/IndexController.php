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
use Application\Model\CategoryRepositoryInterface;

class IndexController extends AbstractActionController
{
    /**
     * @var TestRepositoryInterface
     */
    private $testRepository;
    private $categoryRepository;

    public function __construct(TestRepositoryInterface $testRepository, CategoryRepositoryInterface $categoryRepository)
    {
        $this->testRepository = $testRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function onDispatch(MvcEvent $e) 
    {
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);
        
        $servicemanager = $e->getApplication()->getServiceManager();

        $this->categoryRepository = $servicemanager->get(CategoryRepositoryInterface::class);
        
        $category = $this->categoryRepository->findCategory(29);
        
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
            'first' => $this->testRepository->findTest(4),
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
    
    public function ajaxAction()
    {
        //exit(print_r($this->params()->fromRoute()->id));
        $id=$this->params()->fromRoute('id', '');
        $post = $this->getRequest()->getPost();
        //$id=$this->getRequest()->getQuery('id', 0);
        /*$category = $this->categoryRepository->findCategory(302);
        $id = $category->getId();
        $name = $category->getName();
        //$name = $category->
        $categories = $this->categoryRepository->findAllCategories();
        echo $name.$categories;
        exit;
        return json_encode(['banzaii' => 'vonzaii']);*/
        if ($id=="toweb"){
            $url="http://SRV02:8000/SC/hs/site/get_product";
            /*$headers=get_headers($url);
             * if ($headersss[0] != "HTTP/1.0 200 OK")	{ echo "<pre>" ; exit (print_r($headers));} 
             /**/
            $params = array('name' => 'value');
            $result = file_get_contents(
                    $url, 
                    false, 
                    stream_context_create(array(
            'http' => array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query($params))	
                    )));
            $return.="<pre>";
            $return.= date("r")."\n" ;
            // if($result) print_r(json_decode($result));
            $return.="{$post -> name} => {$post -> value}";
            $return.="</pre>";
             exit ($return);
        }
        if ($id=="to1c"){
                
                //$provider = $this->providerRepository->findAll();
                if (!$list or !is_object($list)) exit(date("r")."<h3>Объект provider не&nbsp;получен</h3>"); 
                $return.=date("r");	
                $return.="<ul>";
                foreach ($list as $row)
                    $return.="<li><a href=# rel='{$row -> id}' class=providers-list >'{$row -> title}'</a></li>";
                $return.="</ul>";
                exit ($return);
        }	
    
        //header('HTTP/1.0 404 Not Found');
        exit(); 
    }
}
