<?php

// ControlPanel/src/Controller/ListController.php

declare(strict_types=1);

namespace ControlPanel\Controller;

use ControlPanel\Service\HtmlContentProvider;
//use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use ControlPanel\Controller\AbstractControlPanelActionController;

class ListController extends AbstractControlPanelActionController // AbstractActionController
{

    private const ROWS_PER_PAGE = 2;

    /** @var ContainerInterface */
    protected $container;

    /** @var Container */
    protected $sessionContainer;

    /** @var HtmlContentProvider */
    protected $htmlContentProvider;

    /** @var laminas.entity.manager */
    protected $entityManager;

    /** @var RbacManager */
    protected $rbacManager;

    /** @var UserManager */
    protected $userManager;

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
        $this->rbacManager = $this->container->get(\ControlPanel\Service\RbacManager::class);
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
//        $hasIdentity = $this->authService->hasIdentity();
//        if (!$hasIdentity) {
//            $this->redirect()->toUrl('/control-panel/login?returnUrl=/control-panel');
//        }
        return $response;
    }

    /**
     * Get products from 1c, store them in cache and show on browser
     *
     * @return JsonModel
     */
    public function showListAction()
    {

        $routeMatch = $this->getEvent()->getRouteMatch();
        $result = $this->getManagerInfo($routeMatch);
        $manager = $result['manager'];
        $managerName = $result['manager_name'];
        
        $post = $this->getRequest()->getPost()->toArray();
        //$post['is_test'] = true;
//        $is_test = 'false';
//        if(isset($post['is_test']) && true == $post['is_test']) {
//            $is_test = 'true';
//        }
        $isTest = $result['is_test'];
        $prefix = $result['prefix'];
        $useCache = $post['use_cache'];

        $identity = $this->authService->getIdentity();

//        if(null == $identity) {
//            return new JsonModel(['data' => [], 'http_code' => '403']);
//        }
//
//        $user = $this->userManager->findOne(['provider_id' => $identity['provider_id'], 'login' => $identity['login'],]);
//
//        $this->rbacManager->init(true);
//        foreach($user['roles'] as $role) {
//            // We need find all permissions for this $role
//            // meanwhile we use administrator permission
//            $access = $this->rbacManager->isGranted($user, 'administrator');
//            if (!$access) {
//                $this->getResponse()->setStatusCode(403);
//                return;
//            }            
//        }

//        $utf8 = mb_convert_encoding($identity['login'], "UTF-8", "Windows-1251 (CP1251)");
        $credentials = ['partner_id: ' . $identity['provider_id'], 'login: ' . $identity['login'], 'is_test: '.$isTest/*, 'is_test: true'*/];
//        $credentials[] = "Accept-Language: ru-RU,ru;q=0.9,en;q=0.8";
//        $credentials[] = "Accept-Charset: utf-8, *;q=0.1"; // windows-1251
//        $credentials[] = "Accept-Encoding: deflate, identity, *;q=0";
        
        $url = $this->config['parameters']['1c_provider_links'][$managerName];

        $answer['http_code'] = '200';
        if (true /* != $useCache */) {
            $answer = $manager->loadAll($url, $credentials);
        }

        $manager->setPageSize(!empty($post['rows_per_page']) ? (int) $post['rows_per_page'] : self::ROWS_PER_PAGE);
        $where = [
            'provider_id' => $identity['provider_id'],
        ];
        $pageNo = isset($post['page_no']) ? $post['page_no'] : 1;
        $cursor = $manager->findDocuments(['pageNo' => $pageNo, 'where' => $where, 'sort' => ['id' => -1], ]);

        return new JsonModel(['data' => $cursor, 'http_code' => $answer['http_code']]);
    }

    /**
     * Show list from collection stored in cache
     *
     * @return JsonModel
     */
    public function showListFromCacheAction()
    {
        $routeMatch = $this->getEvent()->getRouteMatch();
        $result = $this->getManagerInfo($routeMatch);
        $manager = $result['manager'];
        //$managerName = $result['manager_name'];

        $post = $this->getRequest()->getPost()->toArray();
        $identity = $this->authService->getIdentity();
        $manager->setPageSize(!empty($post['rows_per_page']) ? (int) $post['rows_per_page'] : self::ROWS_PER_PAGE);
        $where = [
            'provider_id' => $identity['provider_id'],
        ];
        foreach ($post['filters'] as $key => $value) {
            if ('' !== $value) {
                $where[$key] = $value;
            }
        }
        if (!empty($post['search'])) {
            $where = array_merge($where, ['title' => ['$regex' => $post['search'], '$options' => 'i'],]);
        }
        $cursor = $manager->findDocuments(['pageNo' => $post['page_no'], 'where' => $where, 'sort' => ['id' => -1],]);
        return new JsonModel(['data' => $cursor,]);
    }

}
