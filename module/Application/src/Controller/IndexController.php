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
//        echo '<pre>';
//        //print_r($this->categoryRepository->findAllCategories());
//        $categories = $this->categoryRepository->findAllCategories();
//        print_r($categories);
//                
//        echo '</pre>';
//        exit;
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
        $this->layout()->setTemplate('layout/preview');

        //$category = $this->categoryRepository->findCategory(276745);
//        $test = $this->categoryRepository->findTest(4);

        $categories = $this->categoryRepository->findAllCategories();
        return new ViewModel([
            'menu' => $categories,
//            'first' => $test
        ]);
    }
    
    public function ajaxAction()
    {
        return json_encode(['banzaii' => 'vonzaii']);
    }
}
