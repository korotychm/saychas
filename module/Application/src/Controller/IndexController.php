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

    public function indexAction()
    {
        echo '<pre>';
        //print_r($this->categoryRepository->findAllCategories());
        $categories = $this->categoryRepository->findAllCategories();
        print_r($categories);
//        foreach($categories as $category) {
//            print_r($category);
//        }
                
        echo '</pre>';
        exit;
//        $tests = $this->testRepository->findAllTests();
//        
//        foreach($tests as $test) {
//            echo "{$test->getId()} : {$test->getName()}<br/>";
//        }
        
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
//        
//        foreach($results as $r) {
//            print_r($r);
//        }
        return new ViewModel([
            'tests' => $this->testRepository->findAllTests(),
            'first' => $this->testRepository->findTest(4),
        ]);
    }
    
    public function previewAction()
    {
        $menu = <<<EOL
<ul>
  <li>Microsoft</li>
  <li>Google
  <ul>
  <li>Microsoft</li>
  <li>Google
  <ul>
  <li>Microsoft</li>
  <li>Google</li>
  <li>Apple</li>
  <li>IBM</li>
</ul></li>
  <li>Apple</li>
  <li>IBM</li>
</ul></li>
  <li>Apple</li>
  <li>IBM</li>
</ul>                
EOL;
        return new ViewModel([
            'menu' => $menu,
        ]);
    }
}
