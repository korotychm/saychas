<?php

// ControlPanel/src/Controller/ProductController.php

declare(strict_types=1);

namespace ControlPanel\Controller;

use ControlPanel\Service\HtmlContentProvider;
use Laminas\Mvc\Controller\AbstractActionController;
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

    /** @var laminas.entity.manager */
    protected $entityManager;

    /** @var UserManager */
    protected $userManager;

    /** @var ProductManager */
    protected $productManager;
    
    /** @var CategoryRepository */
    protected $categoryRepository;

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
    public function __construct($container, $sessionContainer, $entityManager, $categoryRepository)
    {
        $this->container = $container;
        $this->sessionContainer = $sessionContainer;
        $this->htmlContentProvider = $this->container->get(HtmlContentProvider::class);
        $this->authService = $this->container->get('my_auth_service');
        $this->entityManager = $entityManager;
        $this->userManager = $this->container->get(\ControlPanel\Service\UserManager::class);
        $this->productManager = $this->container->get(\ControlPanel\Service\ProductManager::class);
        $this->config = $container->get('Config');
        $this->categoryRepository = $categoryRepository;
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
     * Show products action
     * Shows a table of products
     *
     * @return JsonModel
     */
    public function showProductsAction()
    {
        $this->assertLoggedIn();
        //$pageNo = $this->params()->fromRoute('page_no', '1');
        $post = $this->getRequest()->getPost()->toArray();
        $useCache = $post['use_cache'];

        $identity = $this->authService->getIdentity();
        $credentials = ['partner_id: ' . $identity['provider_id'], 'login: ' . $identity['login']];
        $url = $this->config['parameters']['1c_provider_links']['lk_product_info'];

        $answer['http_code'] = '200';
        if (true /* != $useCache */) {
            $answer = $this->productManager->loadAll($url, $credentials);
        }

        $this->productManager->setPageSize(!empty($post['rows_per_page']) ? (int) $post['rows_per_page'] : self::PRODUCTS_PER_PAGE);
        $where = [
            'provider_id' => $identity['provider_id'],
        ];
        $cursor = $this->productManager->findDocuments(['pageNo' => $post['page_no'], 'where' => $where]);
        return new JsonModel(['data' => $cursor, 'http_code' => $answer['http_code']]);
    }

    /**
     * Show products from cache
     *
     * @return JsonModel
     */
    public function showProductsFromCacheAction()
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
        $pageNo = isset($post['page_no']) ? $post['page_no'] : 1;
        $cursor = $this->productManager->findDocuments(['pageNo' => $pageNo, 'where' => $where]);
        return new JsonModel(['data' => $cursor,]);
    }
    
    public function editProductAction()
    {
        $categoryTree = $this->categoryRepository->categoryTree("", 0, $this->params()->fromRoute('id', ''));
        return new JsonModel(['category_tree' => $categoryTree]);
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
        if ($this->canUpdateProduct($post)) {
            $result = $this->productManager->updateDocument([
                'where' => ['id' => $post['product_id']],
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
        if (!$this->authService->hasIdentity()) {
            return new JsonModel(['data' => false]);
        }
    }

}

//$pageNo = $post['page_no'];
//        $rowsPerPage = $post['rows_per_page'];
//        $filters = $post['filters'];
//        $search = $post['search'];
