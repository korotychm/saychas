<?php

// ControlPanel/src/Controller/IndexController.php

declare(strict_types=1);

namespace ControlPanel\Controller;

use ControlPanel\Service\HtmlContentProvider;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Mvc\MvcEvent;

class IndexController extends AbstractActionController
{

    protected $container;
    protected $htmlContentProvider;
    protected $table = [
            ['id' => '00001', 'title' => 'MVideo', 'address' => 'Адрес1', 'geox' => '33.234234', 'geoy' => '33.44444', 'description' => 'description1', 'active' => 'active',],
            ['id' => '00002', 'title' => 'Baramba', 'address' => 'Адрес2', 'geox' => '33.234234', 'geoy' => '33.44444', 'description' => 'description2', 'active' => '',],
            ['id' => '00003', 'title' => 'Shmaramba', 'address' => 'Адрес3', 'geox' => '33.234234', 'geoy' => '33.44444', 'description' => 'description3', 'active' => '',],
            ['id' => '00004', 'title' => 'Obamba', 'address' => 'Адрес4', 'geox' => '33.234234', 'geoy' => '33.44444', 'description' => 'description4', 'active' => '',],
        ];

    public function __construct($container)
    {
        $this->container = $container;
        $this->htmlContentProvider = $this->container->get(HtmlContentProvider::class);
    }

    public function onDispatch(MvcEvent $e)
    {
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);
//        $servicemanager = $e->getApplication()->getServiceManager();
//        $e->getApplication()->getMvcEvent()->getViewModel()->setVariable('category', $category );
        // Return the response
        $menuItems = $this->htmlContentProvider->getMenuItems();
        $sidebarMenuItems = $this->htmlContentProvider->getSidebarMenuItems();
        $this->layout()->setVariables([
            'menuItems' => $menuItems,
            'sidebarMenuItems' => $sidebarMenuItems,
        ]);

        return $response;
    }

    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function showStoresAction()
    {
        $view = new ViewModel(['table' => $this->table]);
        //$view->setTemplate('control-panel/index/index.phtml');
        return $view->setTerminal(true);
    }
    
    public function showOneStoreAction()
    {
        $params = $this->params()->fromRoute();
        foreach($this->table as $row) {
            if($row['id'] == $params['id']) {
                break;
            }
        }
        $view = new ViewModel(['row' => $row]);
        $view->setTemplate('control-panel/index/partials/stores/edit-form.phtml');
        return $view->setTerminal(true);
    }
    
    public function showProductsAction()
    {
        $view = new ViewModel();
        return $view->setTerminal(true);
    }

}
