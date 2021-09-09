<?php

// ControlPanel/src/Controller/ApiController.php

declare(strict_types=1);

namespace ControlPanel\Controller;

use ControlPanel\Service\HtmlContentProvider;
//use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use ControlPanel\Controller\AbstractControlPanelActionController;

class ApiController extends AbstractControlPanelActionController // AbstractActionController
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
    
    public function indexAction()
    {
        return new ViewModel();
    }

}
