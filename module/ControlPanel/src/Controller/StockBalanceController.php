<?php

// ControlPanel/src/Controller/StockBalanceController.php

declare(strict_types=1);

namespace ControlPanel\Controller;

use ControlPanel\Service\HtmlContentProvider;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;

class StockBalanceController extends AbstractActionController
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
    
    /** @var StockBalanceManager */
    protected $stockBalanceManager;

    /** @var AuthenticationService */
    protected $authService;

    /** @var Config */
    protected $config;

    /** @var RbacManager */
    protected $rbacManager;

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
        $this->stockBalanceManager = $this->container->get(\ControlPanel\Service\StockBalanceManager::class);
        $this->config = $container->get('Config');
        $this->rbacManager = $this->container->get(\ControlPanel\Service\RbacManager::class);
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
        return $response;
    }
    
    private function canUpdateStockBalance(array $stockBalance): bool
    {
        $identity = $this->authService->getIdentity();
        $isTest = 'false';
        $credentials = ['partner_id: ' . $identity['provider_id'], 'login: ' . $identity['login'], 'is_test: ' . $isTest/* , 'is_test: true' */];
        $result = $this->stockBalanceManager->updateServerDocument($credentials, $stockBalance);
        $res = $result['http_code'] === 200 && $result['data']['result'] === true;
        return $res;
    }

    public function updateStockBalanceAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        //$product = json_decode($post['data']['product'], true);
        //unset($product['_id']);
        if ($this->canUpdateStockBalance($post)) {
            $result = $this->stockBalanceManager->replaceStockBalance($post);
            return new JsonModel(['result' => true]);
        }
        return new JsonModel(['result' => false]);
    }
    
    public function showStockBalanceAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        print_r($post);
        exit;
    }

}
