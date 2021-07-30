<?php

// ControlPanel/src/Controller/AuthController.php

declare(strict_types=1);

namespace ControlPanel\Controller;

use ControlPanel\Service\HtmlContentProvider;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use Laminas\Authentication\Result;

class AuthController extends AbstractActionController
{

    /** @var ContainerInterface */
    protected $container;

    /** @var HtmlContentProvider */
    protected $htmlContentProvider;

    /** @var Container */
    protected $sessionContainer;

    /** @var EntityManager */
    protected $entityManager;

    /** @var UserManager */
    protected $userManager;

    /** @var AuthManager */
    protected $authManager;
    
    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct($container, $sessionContainer, $entityManager, $userManager, $authManager)
    {
        /** @var ContainerInterface */
        $this->container = $container;
        $this->sessionContainer = $sessionContainer;
        $this->htmlContentProvider = $this->container->get(HtmlContentProvider::class);
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
        $this->authManager = $authManager;
    }

    /**
     * Dispatch events
     *
     * @param MvcEvent $e
     * @return Laminas\Http\Response
     */
    public function onDispatch(MvcEvent $e)
    {
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);
        $this->layout()->setTemplate('layout/control-panel-auth');
        return $response;
    }

    /**
     * Login get request
     * Render a form to enter login details
     *
     * @return ViewModel
     */
    public function loginAction()
    {
//        $answer = $this->getAllUsers([
//            'provider_id' => '00002',
////            'login' => 'Banzaii',
////            'password' => '1234', // 'ODx7hdsjK9',
////            'roles' => ["000000002", "000000003", "000000003",],
////            'access_is_allowed' => true,
//        ]);
        
        //$returnUrl = $this->params()->fromQuery('returnUrl');
        $returnUrl = $this->params()->fromQuery('p');
        return new ViewModel(['action' => '/control-panel/check-login', 'returnUrl' => $returnUrl]);
    }

    /**
     * Check login credentials
     * Unset session partnerLoggedIn data if wrong credentials given
     *
     * @return Redirect
     */
    public function checkLoginAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        $result = $this->authManager->login(['provider_id' => '00002', 'login' => $post['username'], 'password' => $post['password'],]);
        if($result->getCode() == Result::SUCCESS) {
            // set session
            return $this->redirect()->toUrl('/control-panel?'/*$post['returnUrl']*/);
        }
        
        return $this->redirect()->toUrl('/control-panel'/*$post['returnUrl']*/);
    }

    /**
     * Logout
     *
     * @return Redirect
     */
    public function logoutAction()
    {
        $this->authManager->logout();
        return $this->redirect()->toUrl('/control-panel');
    }

    /**
     * Set 403: "Forbidden" status 
     *
     * @return ViewModel
     */
    public function notAuthorizedAction()
    {
        $response = $this->getResponse();
        $response->setStatusCode(403);
        
        return $response;
    }

    /**
     *  Displays the "Not Authorized" page.
     * 
     * @return ViewModel
     */
    public function notAuthorizedViewAction()
    {
        return (new ViewModel())->setTerminal(true);
    }

}
