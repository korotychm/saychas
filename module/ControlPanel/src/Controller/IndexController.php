<?php

// ControlPanel/src/Controller/IndexController.php

declare(strict_types=1);

namespace ControlPanel\Controller;

use ControlPanel\Service\HtmlContentProvider;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Mvc\MvcEvent;

class IndexController extends AbstractActionController
{

    protected $container;
    protected $htmlContentProvider;

    public function __construct($container)
    {
        $this->container = $container;
        $this->htmlContentProvider = $this->container->get(HtmlContentProvider::class);
    }

    public function onDispatch(MvcEvent $e)
    {
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);
//        $servicemanager = $e->getApplication()->getServiceManager();
//        $e->getApplication()->getMvcEvent()->getViewModel()->setVariable('category', $category );
        // Return the response
        $menuItems = $this->htmlContentProvider->getMenuItems();
        $this->layout()->setVariables([
            'menuItems' => $menuItems,
        ]);

        return $response;
    }

    public function indexAction()
    {
        $routeMatch = $this->getEvent()->getRouteMatch();

        $routeName = $routeMatch->getMatchedRouteName();

        return new ViewModel(['routeName' => $routeName,]);
    }

}
