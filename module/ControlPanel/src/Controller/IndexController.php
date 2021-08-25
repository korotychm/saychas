<?php

// ControlPanel/src/Controller/IndexController.php

declare(strict_types=1);

namespace ControlPanel\Controller;

use ControlPanel\Service\HtmlContentProvider;
use ControlPanel\Service\RbacManager;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
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
    
    /** @var RbacManager */
    protected $rbacManager;
    
    protected $entityManager;
    
    protected $userManager;
    
    protected $productManager;
    
    protected $storeManager;

    protected $authService;

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
    public function __construct($container, $sessionContainer, $entityManager/*, $userManager*/)
    {
        $this->container = $container;
        $this->sessionContainer = $sessionContainer;
        $this->htmlContentProvider = $this->container->get(HtmlContentProvider::class);
//        $this->rbacAssertionManager = $this->container->get(\ControlPanel\Service\RbacAssertionManager::class);
        $this->rbacManager = $this->container->get(\ControlPanel\Service\RbacManager::class);
        $this->authService = $this->container->get('my_auth_service');
        $this->entityManager = $entityManager;
        $this->userManager = $this->container->get(\ControlPanel\Service\UserManager::class);
        $this->productManager = $this->container->get(\ControlPanel\Service\ProductManager::class);
        $this->storeManager = $this->container->get(\ControlPanel\Service\StoreManager::class);
        $this->rbacManager->init(true);        
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
            'currentUser' => $this->currentUser(),
        ]);

        $hasIdentity = $this->authService->hasIdentity();
        if(!$hasIdentity) {
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
        //$roleRepository = $this->entityManager->getRepository(\ControlPanel\Model\Entity\Role::class);
//        $this->rbacManager->init(true);
        $currentUser = $this->currentUser();
        $access = $this->access('analyst');
        return new ViewModel(['access' => $access, 'permissionName' => 'developer', 'currentUser' => $currentUser]);
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
        $dateTime = new \DateTime();
        //$stores = $this->storeManager->getAll();
        $view = new ViewModel(['table' => $this->table, 'dateTime' => $dateTime,]);
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
        $identity = $this->authService->getIdentity();
//        $credentials = ['partner_id: 00002', 'login: Banzaii'];
        $credentials = ['partner_id: '.$identity['provider_id'], 'login: '.$identity['login']];
        $answer = $this->productManager->loadAll($credentials);
        $view = new ViewModel(['products' => $answer['data'], 'http_code' => $answer['http_code']]);
//        $view = new ViewModel();
        return $view->setTerminal(true);
    }
    
    /**
     * Show provider profile
     * 
     * @return ViewModel
     */
    public function profileAction()
    {
        $this->assertLoggedIn();
//        if(!$this->authService->hasIdentity()) {
//            return new JsonModel(['data' => false]);
//        }
        return (new ViewModel())->setTerminal(true);
    }
    
    public function userManagementAction()
    {
        $this->assertLoggedIn();
        return (new ViewModel())->setTerminal(true);
    }

    /**
     * Show action and discount page
     * 
     * @return ViewModel
     */
    public function actionAndDiscountAction()
    {
        $this->assertLoggedIn();
        return (new ViewModel())->setTerminal(true);
    }
    
    /**
     * Show account management page
     * 
     * @return ViewModel
     */
    public function accountManagementAction()
    {
        $this->assertLoggedIn();
        return (new ViewModel())->setTerminal(true);
    }
    
    /**
     * Show responding to reviews
     * 
     * @return ViewModel
     */
    public function respondingToReviewsAction()
    {
        $this->assertLoggedIn();
        return (new ViewModel())->setTerminal(true);
    }
    
    public function calendarDetailsAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        $this->assertLoggedIn();
        return (new ViewModel(['day'=>$post['day'], 'month'=>$post['month'], 'year'=>$post['year']]))->setTerminal(true);
    }

    /**
     * Signal ajax script
     * if provider is not logged in
     */
    private function assertLoggedIn()
    {
        if(!$this->authService->hasIdentity()) {
            return new JsonModel(['data' => false]);
        }
//        $identity = $this->authService->getIdentity();
//        $hasIdentity = $this->authService->hasIdentity();
//        $identity2 = $this->identity();
        //if(!isset($this->sessionContainer->partnerLoggedIn)){
//        if(!$hasIdentity) {
//            echo 'null';
//            exit;
//        }
    }

}
