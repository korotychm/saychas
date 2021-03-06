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

    //private const PRODUCTS_PER_PAGE = 2;

    private const STORES_PER_PAGE = 2;

    /** @var ContainerInterface */
    protected $container;

    /** @var Container */
    protected $sessionContainer;

    /** @var HtmlContentProvider */
    protected $htmlContentProvider;

    /** @var RbacManager */
    protected $rbacManager;

    /** @var laminas.entity.manager */
    protected $entityManager;

    /** @var UserManager */
    protected $userManager;

    /** @var ProductManager */
    protected $productManager;

    /** @var StoreManager */
    protected $storeManager;

    /** @var AuthService */
    protected $authService;

    /** Config */
    protected $config;

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
    public function __construct($container, $sessionContainer, $entityManager/* , $userManager */)
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
        $this->config = $container->get('Config');
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
        if (!$hasIdentity) {
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
        //$currentUser = $this->currentUser();
        //$access = $this->access('analyst');
        //$access = $this->rbacManager->isGranted(null, 'analyst'/*, ['banzaii' => 'vonzaii']*/);
        //return new ViewModel(['access' => $access, 'permissionName' => 'developer', 'currentUser' => $currentUser]);
        return new ViewModel();
    }

    /**
     * Show provider profile
     *
     * @return ViewModel
     */
    public function profileAction()
    {
//        if(!$this->authService->hasIdentity()) {
//            return new JsonModel(['data' => false]);
//        }
        return (new ViewModel())->setTerminal(true);
    }

    public function userManagementAction()
    {
        return (new ViewModel())->setTerminal(true);
    }

    /**
     * Show action and discount page
     *
     * @return ViewModel
     */
    public function actionAndDiscountAction()
    {
        return (new ViewModel())->setTerminal(true);
    }

    /**
     * Show account management page
     *
     * @return ViewModel
     */
    public function accountManagementAction()
    {
        return (new ViewModel())->setTerminal(true);
    }

    /**
     * Show responding to reviews
     *
     * @return ViewModel
     */
    public function respondingToReviewsAction()
    {
        return (new ViewModel())->setTerminal(true);
    }

    public function calendarDetailsAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        return (new ViewModel(['day' => $post['day'], 'month' => $post['month'], 'year' => $post['year']]))->setTerminal(true);
    }

//{
//    "credentials": {
//        "partner_id": "00003",
//        "login": "admin"
//    }
//}

    public function confirmOfferAction()
    {
        $identity = $this->authService->getIdentity();
        $credentials = ['credentials' => ['partner_id' => $identity['provider_id'], 'login' => $identity['login']]];
        $result = $this->userManager->confirmOffer($credentials);
        //return new JsonModel(['show_popup' => $answer['result']]);
        $answer = [
            'http_code' => $result['http_code'],
            'result' => $result['result'],
            'error_description' => $result['error_description'],
            'error_description_for_user' => $result['error_description_for_user'],
        ];
        
        return new JsonModel($answer);
        // return new JsonModel(['show_popup' => $answer['result']]);
    }

}

//$cursor = $this->productManager->findAll(['pageNo' => 1, 'filter' => $filter]);































//    /************************************************************************************
//     * Product Actions
//     ************************************************************************************/
//    /**
//     * Show products action
//     * Shows a table of products
//     *
//     * @return ViewModel
//     */
//    public function showProductsAction()
//    {
//
//        //$pageNo = $this->params()->fromRoute('page_no', '1');
//        $post = $this->getRequest()->getPost()->toArray();
//        $pageNo = $post['page_no'];
//
//        $this->assertLoggedIn();
//        $identity = $this->authService->getIdentity();
//        $credentials = ['partner_id: '.$identity['provider_id'], 'login: '.$identity['login']];
//        $url = $this->config['parameters']['1c_provider_links']['lk_product_info'];
//        $answer = $this->productManager->loadAll($url, $credentials);
//        $this->productManager->setPageSize(self::PRODUCTS_PER_PAGE);
//        $where = [
//            'provider_id' => $identity['provider_id'],
//        ];
//        $cursor = $this->productManager->findDocuments(['pageNo' => $pageNo, 'where' => $where]);
//        //$this->productManager->findTest();
//
//        // json_encode($cursor, JSON_UNESCAPED_UNICODE)
//        $view = new ViewModel(['products' => $cursor, 'http_code' => $answer['http_code']]);
//        return $view->setTerminal(true);
//    }
//
//    private function canUpdateProduct($params)
//    {
//        return true;
//    }
//
//    private function canDeleteProduct($params)
//    {
//        return true;
//    }
//
//    public function updateProductAction()
//    {
//        $post = $this->getRequest()->getPost()->toArray();
////        $result = $this->productManager->updateDocument([
////            'where' => ['id' => $post['product_id']/*'000000000001'*/, 'characteristics.id' => '000000008' ],
////            'set' => ['characteristics.$.value' => '0.1345']
////        ]);
//        if($this->canUpdateProduct($post)) {
//            $result = $this->productManager->updateDocument([
//                'where' => ['id' => $post['product_id'] ],
//                'set' => [
//                    'category_id' => $post['category_id'],
//                    'brand_id' => $post['brand_id'],
//                    'color' => $post['color'],
//                    'country' => $post['country'],
//                    'title' => $post['title']
//                ],
//            ]);
//        }
//        //$result = $this->productManager->updateDocument([ 'where' => ['id' => '000000000001', ], 'set' => ['description' => 'Huiption'] ]);
//        return $result;
//    }








//    /**
//     * showOneStoreAction
//     * Shows one store specified by id/login?returnUrl=/control-panel
//     *
//     * @return ViewModel
//     */
//    public function showOneStoreAction()
//    {
//        $params = $this->params()->fromRoute();
//        $this->assertLoggedIn();
//        foreach ($this->table as $row) {
//            if ($row['id'] == $params['id']) {
//                break;
//            }
//        }
//        $view = new ViewModel(['row' => $row]);
//        $view->setTemplate('control-panel/index/partials/stores/edit-form.phtml');
//        return $view->setTerminal(true);
//    }
//
