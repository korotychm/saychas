<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Model;
use Interop\Container\ContainerInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Exception;
//use Laminas\Uri\Exception\InvalidArgumentException;

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
     * @throws InvalidArgumentException
     */
    public function receiveAction()
    {
        $request = $this->getRequest();
        $post = $request->getPost();
        
        $category = $post['value'];
        //$category = '{"a":1,"b":2,"c":3,"d":4,"e":5}';
        //print_r($category);
        print_r(json_decode($category, true));
        exit;
        $categoryRepository = $this->container->get(\Application\Model\CategoryRepositoryInterface::class);
        if(! $categoryRepository instanceof Model\CategoryRepositoryInterface) {
            throw new Exception('Wrong repository');
        }
        $categories = $categoryRepository->findAllCategories();
        return new ViewModel(['categories' => $categories]);
    }
    
}
