<?php

declare(strict_types=1);

namespace Application\Controller;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Mvc\MvcEvent;

class ReceivingController extends AbstractActionController
{
    /**
     * 
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * Assigns $container to a private variable
     * in order to obtain data on the fly
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Receives data from 1c and distributes them among repositories
     * @return ViewModel
     */
    public function receiveAction()
    {
        $categoryRepository = $this->container->get(\Application\Model\CategoryRepositoryInterface::class);
        print_r($categoryRepository->addCategories([]));
        exit;
        return new ViewModel();
    }
    
}
