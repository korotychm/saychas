<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Model;
use Interop\Container\ContainerInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use InvalidArgumentException;
use RuntimeException;
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
     * @throws Exception
     */
    public function receiveAction()
    {
        $request = $this->getRequest();
        $post = $request->getPost();
        
        $category = $post['value'];
        //$category = '{"a":1,"b":2,"c":3,"d":4,"e":5}';
        //print_r($category);
//        print_r(json_decode($category, true));
//        exit;
        $json = $this->getRequest()->getPost()['value'];
        $result = json_decode($json, true);

        $categoryRepository = $this->container->get(\Application\Model\CategoryRepositoryInterface::class);
        if(! $categoryRepository instanceof Model\CategoryRepositoryInterface) {
            throw new InvalidArgumentException('Wrong repository');
        }
        $returnCode = $categoryRepository->addCategories($result);
        return new ViewModel(['returnCode' => $returnCode]);
    }
    
    public function showProviderAction()
    {
        $id = $this->params()->fromRoute('id' , '1');
        $providerRepository = $this->container->get(\Application\Model\ProviderRepositoryInterface::class);
        
        try {
            $provider = $providerRepository->find($id);
            echo $provider->getId(). '<br/>';
            echo $provider->getTitle(). '<br/>';
            echo $provider->getDescription(). '<br/>';
            echo $provider->getIcon(). '<br/>';
            exit;
        }catch(InvalidArgumentException $e){
            print_r($e->getMessage());
        }        
    }
    
    public function receiveProviderAction()
    {
        $request = $this->getRequest();
        $post = $request->getPost();
        mail('alex@localhost', 'test', print_r($post, true));
        exit;
    }
    
}
