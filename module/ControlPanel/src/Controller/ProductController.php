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

    /** @var RbacManager */
    protected $rbacManager;

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
//        $hasIdentity = $this->authService->hasIdentity();
//        if (!$hasIdentity) {
//            $this->redirect()->toUrl('/control-panel/login?returnUrl=/control-panel');
//        }
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

    /**
     * Edit product
     * returns data to edit product screen
     *
     * @return JsonModel
     */
    public function editProductAction()
    {
        $post = $this->getRequest()->getPost()->toArray();

        //$access = $this->rbacManager->isGranted(null, 'analyst', ['product_id' => $post['product_id']]);
        $access = $this->rbacManager->isGranted(null, 'administrator', ['manager' => \ControlPanel\Service\ProductManager::class, 'where' => ['id' => $post['product_id']]]);

        if (!$access) {
            $this->getResponse()->setStatusCode(403);
            return;
        }

        $product = $this->productManager->findProduct($post['product_id']);

        $categoryTree = $this->categoryRepository->categoryTree("", 0, $this->params()->fromRoute('id', ''));

        //mail('user@localhost', 'accumulator', print_r($categoryTree, true));

        return new JsonModel(['category_tree' => $categoryTree, 'product' => $product]);
    }

    public function addProductAction()
    {
        $categoryTree = $this->categoryRepository->categoryTree("", 0, $this->params()->fromRoute('id', ''));

        return new JsonModel(['category_tree' => $categoryTree]);
    }

    public function categoryTreeAction()
    {
        $categoryTree = $this->categoryRepository->categoryTree("", 0, $this->params()->fromRoute('id', ''));
        //$json = json_encode($categoryTree, true);
        return new JsonModel(['category_tree' => $categoryTree]);
    }

    /**
     * Check to see if product is saved on 1c.
     * If so we can update local database collection (products).
     *
     * @param array $product
     * @return bool
     */
    private function canUpdateProduct(array $product): bool
    {
        $identity = $this->authService->getIdentity();
        $isTest = 'false';
        $credentials = ['partner_id: ' . $identity['provider_id'], 'login: ' . $identity['login'], 'is_test: ' . $isTest/* , 'is_test: true' */];
        $result = $this->productManager->updateServerDocument($credentials, $product);
        $res = $result['http_code'] === 200 && $result['data']['result'] === true;
        return $res;
    }

    private function canAddProduct(array &$product): array
    {
        $identity = $this->authService->getIdentity();
        $isTest = 'false';
        $credentials = ['partner_id: ' . $identity['provider_id'], 'login: ' . $identity['login'], 'is_test: ' . $isTest/* , 'is_test: true' */];
        $result = $this->productManager->addServerDocument($credentials, $product);
        $product = $result['data']['data'];
        $res = $result['http_code'] === 200 && $result['data']['result'] === true;
        return ['result' => $res, 'error_description' => $result['data']['error_description'], 'error_description_for_user' => $result['data']['error_description_for_user']];
        //return $res;
    }

    private function canDeleteProduct($params)
    {
        return true;
    }

    /**
     * Upload product image action
     *
     * @return JsonModel
     */
    public function uploadProductImageAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        $productId = $post['product_id'];
        $providerId = $post['provider_id'];

        $baseUrl = $this->config['parameters']['image_path']['base_url'];
        $uploads = $this->config['parameters']['image_path']['subpath']['cpanel_product'];
        $uploadsDir = 'public' . $baseUrl . '/' . $uploads;
        $error = $_FILES['file']['error'];
        if (UPLOAD_ERR_OK == $error) {
            //$fileName = basename($_FILES['file']['name']);
            $uuid = uniqid("{$providerId}_{$productId}_", true);
            list($type, $ext) = explode('/', $_FILES['file']['type']);
            //$newFileName = "{$providerId}_{$productId}_$uuid.$ext";
            $newFileName = "$uuid.$ext";
            $tmpName = $_FILES['file']['tmp_name'];
            //move_uploaded_file($tmpName, "$uploadsDir/$fileName");
            $result = move_uploaded_file($tmpName, "$uploadsDir/$newFileName");
        }

        return new JsonModel(['image_file_name' => $newFileName]);
    }
    
    public function uploadProductFileAction()
    {
//        $post = $this->getRequest()->getPost()->toArray();
//        $productId = $post['product_id'];
//        $providerId = $post['provider_id'];

        $identity = $this->authService->getIdentity();

        $uploadsDir = $documentPath = $this->documentPath('product', ['provider_id' => $identity['provider_id'] ]);

        $error = $_FILES['file']['error'];
        if (UPLOAD_ERR_OK == $error) {
            $newFileName = $_FILES['file']['name'];
            $tmpName = $_FILES['file']['tmp_name'];
            $result = move_uploaded_file($tmpName, "$uploadsDir/$newFileName");
            // file moved from tmp to permanent location
            // we now need to inform 1C about uploading the file
            
            $identity = $this->authService->getIdentity();
            $credentials = ['partner_id: ' . $identity['provider_id'], 'login: ' . $identity['login'], 'is_test: false'];
            $data = ['filename' => $newFileName, 'query_type' => 'product'];
            $result = $this->productManager->uploadProductFile($credentials, $data);
            $res = $result['http_code'] === 200 && $result['data']['result'] === true;
            
            $this->getResponse()->setStatusCode($result['http_code']);
            return new JsonModel(['result' => true, 'error_description' => '', 'http_code' => $result['http_code']]);
        }

        $this->getResponse()->setStatusCode(400);
        return new JsonModel(['result' => false, 'error_description' => 'error uploading document file', 'http_code' => 400]);
        // return new JsonModel(['file_name' => $newFileName]);
    }
    
    public function getProductFileAnswerAction()
    {
        $request = $this->getRequest();
        $content = $request->getContent();
        return new JsonModel($content);
    }
    

    /**
     * Update product
     * Send product data to 1c first and
     * update locally if canUpdateProduct returns true
     *
     * @return JsonModel
     */
    public function updateProductAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        $product = json_decode($post['data']['product'], true);
        $deletedImages = $product['del_images'];
        $clonedImages = $product['clone_images'];
        $result = ['matched_count' => 0, 'modified_count' => 0];
        if ($this->canUpdateProduct($product)) {
            $result = $this->productManager->replaceProduct($product);
            foreach ($deletedImages as $image) {
                $this->productManager->deleteProductImage($image);
            }
            foreach ($clonedImages as $image) {
                $this->productManager->copyProductImage($image);
            }
            return new JsonModel(['result' => true]);
        }

        return new JsonModel(['result' => false]);
    }

    /**
     * Update price and discount action
     *
     * @return JsonModel
     */
    public function updatePriceAndDiscountAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        $product = json_decode($post['data']['product'], true);
        unset($product['_id']);
        if ($this->canUpdatePriceAndDiscount($product)) {
            $result = $this->productManager->replaceProduct($product);
            return new JsonModel(['result' => true]);
        }

        return new JsonModel(['result' => false]);
    }

    /**
     * Check to see if price and discount can be updated
     *
     * @param array $product
     * @return bool
     */
    private function canUpdatePriceAndDiscount(array $product): bool
    {
        $identity = $this->authService->getIdentity();
        $isTest = 'false';
        $credentials = ['partner_id: ' . $identity['provider_id'], 'login: ' . $identity['login'], 'is_test: ' . $isTest/* , 'is_test: true' */];
        $result = $this->productManager->updateServerPriceAndDiscount($credentials, $product);
        $res = $result['http_code'] === 200 && $result['data']['result'] === true;

        return $res;
    }

    /**
     * Save newly added product action
     *
     * @return JsonModel
     */
    public function saveNewlyAddedProductAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        $product = json_decode($post['data']['product'], true);
        unset($product['brand_id']);
        unset($product['color_id']);
        unset($product['country_id']);
        unset($product['del_images']);
        $res = $this->canAddProduct($product);
        if ($res['result']) {
            $result = $this->productManager->replaceProduct($product);
            return new JsonModel(['result' => true, 'data' => $product]);
        }

        //return new JsonModel(['result' => false]);
        return new JsonModel($res);
    }

    /**
     * Request category characteristics action
     *
     * @return JsonModel
     */
    public function requestCategoryCharacteristicsAction()
    {
        $identity = $this->authService->getIdentity();
        $credentials = ['partner_id: ' . $identity['provider_id'], 'login: ' . $identity['login'], 'is_test: false'/* , 'is_test: true' */];

        $post = $this->getRequest()->getPost()->toArray();
        $data = $post['data'];
        $data['product'] = json_decode($data['product'], true);
        $answer = $this->productManager->requestCategoryCharacteristics($credentials, $data);

        $product = $this->productManager->findProduct2($answer['data']['product']);
        $answer['data']['product'] = $product;

        return new JsonModel(['answer' => $answer]);
    }

    /**
     * Request category characteristics only;
     * Do not request the product "header"
     *
     * @return JsonModel
     */
    public function requestCategoryCharacteristicsOnlyAction()
    {
        $identity = $this->authService->getIdentity();
        $credentials = ['partner_id: ' . $identity['provider_id'], 'login: ' . $identity['login'], 'is_test: false'/* , 'is_test: true' */];

        $post = $this->getRequest()->getPost()->toArray();

        $categoryId = $post['data']['category_id'];
        $data = ['new_category_id' => $categoryId];
        $answer = $this->productManager->requestCategoryCharacteristics($credentials, $data);
        $product = $this->productManager->findProduct2($answer['data']['product']);
        $handbook = $this->productManager->getHandbooks();
        $product['brands'] = $handbook['brands'];
        $product['colors'] = $handbook['colors'];
        $product['countries'] = $handbook['countries'];
        $answer['data']['product'] = $product;

        return new JsonModel(['answer' => $answer]);
    }

    /**
     * Example
     * $credentials = ['partner_id: ' . $identity['provider_id'], 'login: ' . $identity['login'], 'is_test: false'];
     *  $content => [
     *    "shop_id": "",
     *    "category_id": "000000527",
     *    "query_type": "product"
     *  ]
     *
     * @return JsonModel
     */
    public function getProductFileAction()
    {
        $post = $this->getRequest()->getPost()->toArray();

        $identity = $this->authService->getIdentity();

        $content = [
          "provider_id" => $identity['provider_id'],
          "store_id" => '',
          "category_id" => $post['data']['category_id'],
          "query_type" => $post['data']['query_type'],
        ];
        
        /** To be removed */
//        $content = [
////          "provider_id" => $identity['provider_id'],
//          "store_id" => '',
//          "category_id" => '000000006',
//          "query_type" => 'product',
//        ];

        $credentials = ['partner_id: ' . $identity['provider_id'], 'login: ' . $identity['login'], 'is_test: false'];
        //$result = $this->productManager->getProductFile($content);
        $result = $this->productManager->getProductFile($credentials, $content);

        if(false == $result['data']['result']) {
            return new JsonModel(['result' => $result['data']['result'], 'filename' => '', 'error_description' => $result['data']['error_description'], 'http_code' => $result['http_code']]);
        }

        return new JsonModel(['result' => $result['data']['result'], 'filename' => $result['data']['filename'], 'error_description' => '', 'http_code' => '200' ]);
    }

    /**
     *  Example
     *  $content = [
     *    "source_file" => "string",
     *    "result_file" => "string",
     *    "provider_id" => "string",
     *    "query_type" => "item"
     *  ];
     *
     * @return JsonModel
     */
    public function sendProductFileAction()
    {
        $request = $this->getRequest();
        $post = $request->getPost()->toArray();
        $files = $request->getFiles();
        $count = count($files);
        $identity = $this->authService->getIdentity();

        $content = [
          "source_file" => 'смартфоны.xls',// 'bl_product_000000006.xls',
          "result_file" => '',
          "provider_id" => $identity['provider_id'],
          "query_type" => 'new_product', // $post['data']['query_type'],
        ];

        $this->productManager->copyProductFile($content);
        $result = $this->productManager->sendProductInfo($content);

        return new JsonModel(['result' => $result['result'],]);
    }
}
