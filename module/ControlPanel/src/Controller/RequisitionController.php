<?php

// ControlPanel/src/Controller/RequisitionController.php

declare(strict_types=1);

namespace ControlPanel\Controller;

use ControlPanel\Service\HtmlContentProvider;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;

class RequisitionController extends AbstractActionController
{

    // private const STORES_PER_PAGE = 2;

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

    /** @var RequisitionManager */
    protected $requisitionManager;

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
        $this->requisitionManager = $this->container->get(\ControlPanel\Service\RequisitionManager::class);
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

    public function editRequisitionAction()
    {
        $post = $this->getRequest()->getPost()->toArray();

        $access = $this->rbacManager->isGranted(null, 'administrator', ['manager' => \ControlPanel\Service\RequisitionManager::class, 'where' => ['id' => $post['requisition_id']]]);

        if (!$access) {
            $this->getResponse()->setStatusCode(403);
            return;
        }

        $requisition = $this->requisitionManager->find(['id' => $post['requisition_id']]);

        return new JsonModel(['requisition' => $requisition]);
    }

    private function canUpdateRequisition(array $data): array //  bool
    {
        $identity = $this->authService->getIdentity();
        $isTest = 'false';
        $credentials = ['partner_id: ' . $identity['provider_id'], 'login: ' . $identity['login'], 'is_test: ' . $isTest/* , 'is_test: true' */];
        $result = $this->requisitionManager->updateServerDocument($credentials, ['data' => $data] );
        
        return $result;
    }
    
    private function updateStatus(array $data)
    {
        $identity = $this->authService->getIdentity();
        $isTest = 'false';
        $credentials = ['partner_id: ' . $identity['provider_id'], 'login: ' . $identity['login'], 'is_test: ' . $isTest/* , 'is_test: true' */];
        $result = $this->requisitionManager->updateRequisitionStatus($credentials, ['data' => $data] );
        
        return $result;
    }

    public function updateRequisitionAction()
    {
        $requisition = $this->getRequest()->getPost()->toArray();
        $status = ['requisition_id' =>$requisition['id'], 'status' => $requisition['status'], 'status_id' => $requisition['status_id'] ];
        $statusResult = $this->updateStatus($status);
        $statusRes = $statusResult['http_code'] === 200 && $statusResult['data']['result'] === true;
        if(false == $statusRes) {
            $answer = [
                'http_code' => '400',
                'result' => $statusResult['result'],
                'error_description' => $statusResult['error_description'],
                'error_description_for_user' => $statusResult['error_description'],
            ];
            
            return JsonModel($answer);
        }
        
        $result = $this->canUpdateRequisition($requisition);
        $res = $result['http_code'] === 200 && $result['data']['result'] === true;
        unset($requisition['_id']);
        if ($res) {
            // $this->requisitionManager->replaceRequisition($requisition);
            $this->requisitionManager->replaceRequisition($result['data']);
        }

        $answer = [
            'http_code' => $result['http_code'],
            'result' => $result['data']['result'],
            'error_description' => $result['data']['error_description'],
            'error_description_for_user' => $result['data']['error_description_for_user'],
            'data' => $result['data'],
        ];

        return new JsonModel($answer);
    }
    
    public function getRequisitionStatusAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        $status = $this->requisitionManager->getRequisitionStatus($post['id']);
        
        if(false == $status['result']) {
            $answer = [
                'http_code' => '400',
                'result' => $status['result'],
                'error_description' => 'requisition with given id not found',
                'error_description_for_user' => 'requisition with given id not found',
            ];
            
            return new JsonModel($answer);
        }
        
        $answer = [
            'http_code' => '200',
            'result' => true,
            'status' => $status['status'],
            'status_id' => $status['status_id'],
        ];
        return new JsonModel($answer);
    }

}
