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

        $categoryRepository = $servicemanager->get(CategoryRepositoryInterface::class);
        
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

        $category = $this->categoryRepository->findCategory(29);

        $categories = $this->categoryRepository->findAllCategories();
        return new ViewModel([
            'menu' => $categories,
            'first' => $category
        ]);
    }
    
    public function ajaxAction()
    {
        return json_encode(['banzaii' => 'vonzaii']);
    }
}
