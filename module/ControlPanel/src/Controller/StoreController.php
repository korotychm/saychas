<?php

// ControlPanel/src/Controller/StoreController.php

declare(strict_types=1);

namespace ControlPanel\Controller;

use ControlPanel\Service\HtmlContentProvider;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;

class StoreController extends AbstractActionController
{

    private const STORES_PER_PAGE = 2;

    /** @var ContainerInterface */
    protected $container;

    /** @var Container */
    protected $sessionContainer;

    /** @var HtmlContentProvider */
    protected $htmlContentProvider;

    /** @var laminas.entity.manager */
    protected $entityManager;

    /** @var UserManager */
    protected $userManager;

    /** @var StoreManager */
    protected $storeManager;

    /** @var AuthenticationService */
    protected $authService;

    /** @var Config */
    protected $config;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     * @param Laminas\Session\Container $sessionContainer
     */
    public function __construct($container, $sessionContainer, $entityManager)
    {
        $this->container = $container;
        $this->sessionContainer = $sessionContainer;
        $this->htmlContentProvider = $this->container->get(HtmlContentProvider::class);
        $this->authService = $this->container->get('my_auth_service');
        $this->entityManager = $entityManager;
        $this->userManager = $this->container->get(\ControlPanel\Service\UserManager::class);
        $this->storeManager = $this->container->get(\ControlPanel\Service\StoreManager::class);
        $this->config = $container->get('Config');
    }

    /**
     * onDispatch
     *
     * @param MvcEvent $e
     * @return Response
     */
    public function onDispatch(MvcEvent $e)
    {
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);
        $hasIdentity = $this->authService->hasIdentity();
        if (!$hasIdentity) {
            $this->redirect()->toUrl('/control-panel/login?returnUrl=/control-panel');
        }
        return $response;
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
        $identity = $this->authService->getIdentity();
        $credentials = ['partner_id: '.$identity['provider_id'], 'login: '.$identity['login']];
        //$credentials = ['partner_id: '.'00003', 'login: '.'admin'];
        $url = $this->config['parameters']['1c_provider_links']['lk_store_info'];
        $answer = $this->storeManager->loadAll($url, $credentials);

        $this->storeManager->setPageSize(2);
        $cursor = $this->storeManager->findAll(['pageNo' => 1]);
        $view = new ViewModel(['table' => $this->table, 'dateTime' => $dateTime, 'stores' => $cursor /*$answer['data']*/, 'http_code' => $answer['http_code']]);
        return $view->setTerminal(true);
    }

    public function showStoresFromCacheAction()
    {
        $this->assertLoggedIn();
        $post = $this->getRequest()->getPost()->toArray();
        $identity = $this->authService->getIdentity();
        $this->productManager->setPageSize(!empty($post['rows_per_page']) ? (int) $post['rows_per_page'] : self::PRODUCTS_PER_PAGE);
        $where = [
            'provider_id' => $identity['provider_id'],
        ];
        foreach ($post['filters'] as $key => $value) {
            if (!empty($value)) {
                $where[$key] = $value;
            }
        }
        if (!empty($post['search'])) {
            $where = array_merge($where, ['title' => ['$regex' => $post['search'], '$options' => 'i'],]);
        }
        $cursor = $this->productManager->findDocuments(['pageNo' => $post['page_no'], 'where' => $where]);
        return new JsonModel(['data' => $cursor,]);
    }

    /**
     * Signal ajax script
     * if provider is not logged in
     */
    private function assertLoggedIn()
    {
        if (!$this->authService->hasIdentity()) {
            return new JsonModel(['data' => false]);
        }
    }

}
