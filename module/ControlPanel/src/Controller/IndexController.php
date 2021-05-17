<?php

declare(strict_types=1);

namespace ControlPanel\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{

    public function __construct()
    {
    }
/**
    public function onDispatch(MvcEvent $e) 
    {
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);
//        $servicemanager = $e->getApplication()->getServiceManager();
        $userAddressHtml = $this->htmlProvider->writeUserAddress();

//        $this->categoryRepository = $servicemanager->get(CategoryRepositoryInterface::class);
//        $category = $this->categoryRepository->findCategory(29);
//        $e->getApplication()->getMvcEvent()->getViewModel()->setVariable('category', $category );

        // Return the response
        $this->layout()->setVariables([
            'headerText' => $this->htmlProvider->testHtml(),
            'footerText' => 'banzaii',
            'catalogCategoties' => $this->categoryRepository->findAllCategories("", 0, $this->params()->fromRoute('id', '')),
            'userAddressHtml' => $userAddressHtml,
        ]);
        //$this->layout()->setTemplate('layout/mainpage');
        return $response;
        
    }
*/   
    public function indexAction()
    {
        $routeMatch = $this->getEvent()->getRouteMatch();

        $routeName = $routeMatch->getMatchedRouteName();
        
        print_r($routeName);
//
//        exit;
        
        return new ViewModel(['banzaii' => 'vonzaii']);
    }    
    
}
