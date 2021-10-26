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
//        $hasIdentity = $this->authService->hasIdentity();
//        if (!$hasIdentity) {
//            $this->redirect()->toUrl('/control-panel/login?returnUrl=/control-panel');
//        }
        return $response;
    }

    public function editStoreAction()
    {
        $post = $this->getRequest()->getPost()->toArray();

        $access = $this->rbacManager->isGranted(null, 'administrator', ['manager' => \ControlPanel\Service\StoreManager::class, 'where' => ['id' => $post['store_id']]]);

        if (!$access) {
            $this->getResponse()->setStatusCode(403);
            return;
        }
//        $identity = $this->authService->getIdentity();
//
//        $access = $this->rbacManager->isGranted(null, 'administrator', ['provider_id' => $post['provider_id']]);
//        if (!$access) {
//            $this->getResponse()->setStatusCode(403);
//            return;
//        }
//        $post['store_id'] = '000000004';
        $store = $this->storeManager->find(['id' => $post['store_id']]);

        return new JsonModel(['store' => $store]);
    }

    private function canUpdateStore(array $store): array //  bool
    {
        $identity = $this->authService->getIdentity();
        $isTest = 'false';
        $credentials = ['partner_id: ' . $identity['provider_id'], 'login: ' . $identity['login'], 'is_test: ' . $isTest/* , 'is_test: true' */];
        $result = $this->storeManager->updateServerDocument($credentials, $store);
        // $res = $result['http_code'] === 200 && $result['data']['result'] === true;
        return $result; // $res;
    }

    public function updateStoreAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        $store = $post['data']['store'];
        $store['address'] = json_decode($store['address'], true);
        $result = $this->canUpdateStore($store);
        $res = $result['http_code'] === 200 && $result['data']['result'] === true;
        unset($store['_id']);
//        $result = ['matched_count' => 0, 'modified_count' => 0];
        //if ($this->canUpdateStore($store)) {
        if ($res) {
            /*$result =*/ $this->storeManager->replaceStore($store);
            return new JsonModel(['result' => true]);
        }
        return new JsonModel($result);
        // return new JsonModel(['result' => false]);
    }

    private function canAddStore(array &$store): array // bool
    {
        $identity = $this->authService->getIdentity();
        $isTest = 'false';
        $credentials = ['partner_id: ' . $identity['provider_id'], 'login: ' . $identity['login'], 'is_test: ' . $isTest/* , 'is_test: true' */];
        $result = $this->storeManager->addServerDocument($credentials, $store);
        $store = $result['data']['data'];
        //$res = $result['http_code'] === 200 && $result['data']['result'] === true;
        return $result;// $res;
    }

    public function saveNewlyAddedStoreAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        $store = $post['data']['store'];
        $store['address'] = json_decode($store['address'], true);
        $result = $this->canAddStore($store);
        $res = $result['http_code'] === 200 && $result['data']['result'] === true;
        //if ($this->canAddStore($store)) {
        if($res) {
            $this->storeManager->replaceStore($store);
            return new JsonModel(['result' => true, 'data' => $store]);
        }
        return new JsonModel($result);
        //return new JsonModel(['result' => false]);
    }

    /**
     * Show stores action
     *
     * @return JsonModel
     */
//    public function showStores1Action()
//    {
//        $this->assertLoggedIn();
//        $post = $this->getRequest()->getPost()->toArray();
//        $useCache = $post['use_cache'];
//
//        $identity = $this->authService->getIdentity();
//        $credentials = ['partner_id: '.$identity['provider_id'], 'login: '.$identity['login']];
//        $url = $this->config['parameters']['1c_provider_links']['lk_store_info'];
//
//        $answer['http_code'] = '200';
//        if(true /* != $useCache */) {
//            $answer = $this->storeManager->loadAll($url, $credentials);
//        }
//
//        $this->storeManager->setPageSize(!empty($post['rows_per_page']) ? (int) $post['rows_per_page'] : self::STORES_PER_PAGE);
//        $where = [
//            'provider_id' => $identity['provider_id'],
//        ];
//        $pageNo = isset($post['page_no']) ? $post['page_no'] : 1;
//        $cursor = $this->storeManager->findDocuments(['pageNo' => $pageNo, 'where' => $where]);
//
//        return new JsonModel(['data' => $cursor, 'http_code' => $answer['http_code']]);
//
//    }

    /**
     * Temporarily comment out this function
     */
//    public function showStoresAction()
//    {
//
//        $routeMatch = $this->getEvent()->getRouteMatch();
//
//        $routeName = $routeMatch->getMatchedRouteName();
//
//        list($leftName, $rightName) = explode('/', $routeName);
//
//        //$params = $routeMatch->getParams();
//
//        $config = $this->container->get('Config');
//
//        $managerName = $config['router']['routes'][$leftName]['child_routes'][$rightName]['options']['repository'];
//
//        $manager = $this->container->get($managerName);
//
//
//        $post = $this->getRequest()->getPost()->toArray();
//        $useCache = $post['use_cache'];
//
//        $identity = $this->authService->getIdentity();
//        $credentials = ['partner_id: '.$identity['provider_id'], 'login: '.$identity['login']];
//        //$url = $this->config['parameters']['1c_provider_links']['lk_store_info'];
//        $url = $this->config['parameters']['1c_provider_links'][$managerName];
//
//        $answer['http_code'] = '200';
//        if(true /* != $useCache */) {
//            //$answer = $this->storeManager->loadAll($url, $credentials);
//            $answer = $manager->loadAll($url, $credentials);
//        }
//
//        $manager->setPageSize(!empty($post['rows_per_page']) ? (int) $post['rows_per_page'] : self::STORES_PER_PAGE);
//        $where = [
//            'provider_id' => $identity['provider_id'],
//        ];
//        $pageNo = isset($post['page_no']) ? $post['page_no'] : 1;
//        $cursor = $manager->findDocuments(['pageNo' => $pageNo, 'where' => $where]);
//
//        return new JsonModel(['data' => $cursor, 'http_code' => $answer['http_code']]);
//
//    }

    /**
     * Show stores from cache action
     *
     * @return JsonModel
     */
    /**
     * Temporarily comment out this function
     */
//    public function showStoresFromCacheAction()
//    {
//        $routeMatch = $this->getEvent()->getRouteMatch();
//
//        $routeName = $routeMatch->getMatchedRouteName();
//
//        list($leftName, $rightName) = explode('/', $routeName);
//
//        //$params = $routeMatch->getParams();
//
//        $config = $this->container->get('Config');
//
//        $managerName = $config['router']['routes'][$leftName]['child_routes'][$rightName]['options']['repository'];
//
//        $manager = $this->container->get($managerName);
//
//        $post = $this->getRequest()->getPost()->toArray();
//        $identity = $this->authService->getIdentity();
//        $manager->setPageSize(!empty($post['rows_per_page']) ? (int) $post['rows_per_page'] : self::STORES_PER_PAGE);
//        $where = [
//            'provider_id' => $identity['provider_id'],
//        ];
//        foreach ($post['filters'] as $key => $value) {
//            if (!empty($value)) {
//                $where[$key] = $value;
//            }
//        }
//        if (!empty($post['search'])) {
//            $where = array_merge($where, ['title' => ['$regex' => $post['search'], '$options' => 'i'],]);
//        }
//        $cursor = $manager->findDocuments(['pageNo' => $post['page_no'], 'where' => $where]);
//        return new JsonModel(['data' => $cursor,]);
//    }
//    public function showStores1FromCacheAction()
//    {
//        $this->assertLoggedIn();
//        $post = $this->getRequest()->getPost()->toArray();
//        $identity = $this->authService->getIdentity();
//        $this->storeManager->setPageSize(!empty($post['rows_per_page']) ? (int) $post['rows_per_page'] : self::STORES_PER_PAGE);
//        $where = [
//            'provider_id' => $identity['provider_id'],
//        ];
//        foreach ($post['filters'] as $key => $value) {
//            if (!empty($value)) {
//                $where[$key] = $value;
//            }
//        }
//        if (!empty($post['search'])) {
//            $where = array_merge($where, ['title' => ['$regex' => $post['search'], '$options' => 'i'],]);
//        }
//        $cursor = $this->storeManager->findDocuments(['pageNo' => $post['page_no'], 'where' => $where]);
//        return new JsonModel(['data' => $cursor,]);
//    }

    /**
     * Signal ajax script
     * if provider is not logged in
     */
//    private function assertLoggedIn()
//    {
//        if (!$this->authService->hasIdentity()) {
//            return new JsonModel(['data' => false]);
//        }
//    }
}
