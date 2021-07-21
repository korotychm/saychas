<?php

// ControlPanel/src/Controller/IndexController.php

declare(strict_types=1);

namespace ControlPanel\Controller;

use ControlPanel\Service\HtmlContentProvider;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;

class LoginController extends AbstractActionController
{

    /** @var ContainerInterface */
    protected $container;

    /** @var HtmlContentProvider */
    protected $htmlContentProvider;

    /** @var Container */
    protected $sessionContainer;
    
    protected $entityManager;

    protected $userManager;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct($container, $sessionContainer, $entityManager, $userManager)
    {
        /** @var ContainerInterface */
        $this->container = $container;
        $this->sessionContainer = $sessionContainer;
        $this->htmlContentProvider = $this->container->get(HtmlContentProvider::class);
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
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
        $this->layout()->setTemplate('layout/control-panel-login');
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
        $returnUrl = $this->params()->fromQuery('returnUrl');
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
        if ('banzaii' == $post['username'] && 'vonzaii' == $post['password']) {
            // set session
            $this->sessionContainer->partnerLoggedIn = true;

            return $this->redirect()->toUrl($post['returnUrl']);
        }
        unset($this->sessionContainer->partnerLoggedIn);
        return $this->redirect()->toUrl($post['returnUrl']);
    }

    /**
     * Unset session partnerLoggedIn data
     *
     * @return Redirect
     */
    public function logoutAction()
    {
        unset($this->sessionContainer->partnerLoggedIn);
        return $this->redirect()->toUrl('/control-panel');
    }

}
