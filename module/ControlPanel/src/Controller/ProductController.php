<?php

// ControlPanel/src/Controller/ProductController.php

declare(strict_types=1);

namespace ControlPanel\Controller;

use ControlPanel\Service\HtmlContentProvider;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;

class ProductController extends AbstractActionController
{
    
    private const PRODUCTS_PER_PAGE = 2;
    
    /** @var ContainerInterface */
    protected $container;

    /** @var Container */
    protected $sessionContainer;

    /** @var HtmlContentProvider */
    protected $htmlContentProvider;

    protected $entityManager;

    protected $userManager;

    protected $productManager;

    protected $authService;

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
        $this->productManager = $this->container->get(\ControlPanel\Service\ProductManager::class);
        $this->config = $container->get('Config');
    }

    public function onDispatch(MvcEvent $e)
    {
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);
        $hasIdentity = $this->authService->hasIdentity();
        if(!$hasIdentity) {
            $this->redirect()->toUrl('/control-panel/login?returnUrl=/control-panel');
        }
        return $response;
    }

    /**
     * Show products action
     * Shows a table of products
     *
     * @return ViewModel
     */
    public function showProductsAction()
    {
        
        //$pageNo = $this->params()->fromRoute('page_no', '1');
        $post = $this->getRequest()->getPost()->toArray();
        $pageNo = $post['page_no'];
        
        $this->assertLoggedIn();
        $identity = $this->authService->getIdentity();
        $credentials = ['partner_id: '.$identity['provider_id'], 'login: '.$identity['login']];
        $url = $this->config['parameters']['1c_provider_links']['lk_product_info'];
        $answer = $this->productManager->loadAll($url, $credentials);
        $this->productManager->setPageSize(self::PRODUCTS_PER_PAGE);
        $where = [
            'provider_id' => $identity['provider_id'],
        ];
        $cursor = $this->productManager->findDocuments(['pageNo' => $pageNo, 'where' => $where]);
        //$this->productManager->findTest();
        
        print_r(json_encode($cursor, JSON_UNESCAPED_UNICODE));
        exit;
        
//        return new JsonModel(['data' => true, 'http_code' => $answer['http_code']]);
        $view = new ViewModel(['products' => $cursor, 'http_code' => $answer['http_code']]);
        return $view->setTerminal(true);
    }
    
    private function canUpdateProduct($params)
    {
        return true;
    }
    
    private function canDeleteProduct($params)
    {
        return true;
    }

    public function updateProductAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
//        $result = $this->productManager->updateDocument([
//            'where' => ['id' => $post['product_id']/*'000000000001'*/, 'characteristics.id' => '000000008' ],
//            'set' => ['characteristics.$.value' => '0.1345']
//        ]);
        if($this->canUpdateProduct($post)) {
            $result = $this->productManager->updateDocument([
                'where' => ['id' => $post['product_id'] ],
                'set' => [
                    'category_id' => $post['category_id'],
                    'brand_id' => $post['brand_id'],
                    'color' => $post['color'],
                    'country' => $post['country'],
                    'title' => $post['title']
                ],
            ]);
        }
        //$result = $this->productManager->updateDocument([ 'where' => ['id' => '000000000001', ], 'set' => ['description' => 'Huiption'] ]);
        return $result;
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
    }

}
