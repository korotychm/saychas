<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Model;
use Interop\Container\ContainerInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use InvalidArgumentException;
use RuntimeException;

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
     * @throws Exception
     */
    public function receiveAction()
    {
        $json = $this->getRequest()->getPost()['value'];
        $result = json_decode($json, true);
                
        $categoryRepository = $this->container->get(\Application\Model\CategoryRepositoryInterface::class);
        if(! $categoryRepository instanceof Model\CategoryRepositoryInterface) {
            throw new InvalidArgumentException('Wrong repository');
        }
        $returnCode = $categoryRepository->addCategories($result);
        return new ViewModel(['returnCode' => $returnCode]);
    }

}
