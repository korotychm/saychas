<?php

// ControlPanel/src/Controller/IndexController.php

declare(strict_types=1);

namespace ControlPanel\Controller;

use ControlPanel\Service\HtmlContentProvider;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;

class IndexController extends AbstractActionController
{

    /** @var ContainerInterface */
    protected $container;

    /** @var Container */
    protected $sessionContainer;

    /** @var HtmlContentProvider */
    protected $htmlContentProvider;

    /** @var array */
    protected $table = [
        ['id' => '00001', 'title' => 'MVideo', 'address' => 'Адрес1', 'geox' => '33.234234', 'geoy' => '33.44444', 'description' => 'description1', 'active' => 'active',],
        ['id' => '00002', 'title' => 'Baramba', 'address' => 'Адрес2', 'geox' => '33.234234', 'geoy' => '33.44444', 'description' => 'description2', 'active' => '',],
        ['id' => '00003', 'title' => 'Shmaramba', 'address' => 'Адрес3', 'geox' => '33.234234', 'geoy' => '33.44444', 'description' => 'description3', 'active' => '',],
        ['id' => '00004', 'title' => 'Obamba', 'address' => 'Адрес4', 'geox' => '33.234234', 'geoy' => '33.44444', 'description' => 'description4', 'active' => '',],
    ];

    /**
     * Constructor
     * 
     * @param ContainerInterface $container
     * @param Laminas\Session\Container $sessionContainer
     */
    public function __construct($container, $sessionContainer)
    {
        $this->container = $container;
        $this->sessionContainer = $sessionContainer;
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
        $this->layout()->setTemplate('layout/control-panel');
        $this->layout()->setVariables([
            'menuItems' => $menuItems,
            'sidebarMenuItems' => $sidebarMenuItems,
        ]);

        if (!$this->sessionContainer->partnerLoggedIn) {
            $this->redirect()->toUrl('/control-panel/login?returnUrl=/control-panel');
        }
        return $response;
    }

    /**
     * Index action
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * Show stores action        // $this->sessionContainer = new Container(StringResource::CONTROL_PANEL_SESSION);
     * Shows a table of stores
     *
     * @return ViewModel
     */
    public function showStoresAction()
    {
        $this->assertLoggedIn();
        $view = new ViewModel(['table' => $this->table]);
        return $view->setTerminal(true);
    }

    /**
     * showOneStoreAction
     * Shows one store specified by id/login?returnUrl=/control-panel
     *
     * @return ViewModel
     */
    public function showOneStoreAction()
    {
        $params = $this->params()->fromRoute();
        $this->assertLoggedIn();
        foreach ($this->table as $row) {
            if ($row['id'] == $params['id']) {
                break;
            }
        }
        $view = new ViewModel(['row' => $row]);
        $view->setTemplate('control-panel/index/partials/stores/edit-form.phtml');
        return $view->setTerminal(true);
    }

    /**
     * Show products action
     * Shows a table of products
     *
     * @return ViewModel
     */
    public function showProductsAction()
    {
        $this->assertLoggedIn();
        $view = new ViewModel();
        return $view->setTerminal(true);
    }
    
    private function assertLoggedIn()
    {
        if(!isset($this->sessionContainer->partnerLoggedIn)){
            echo 'null';
            exit;
        }
    }

}
