<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Model;
use Interop\Container\ContainerInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use InvalidArgumentException;
//use RuntimeException;
use Exception;
//use Laminas\Db\ResultSet\HydratingResultSet;
//use Laminas\Uri\Exception\InvalidArgumentException;
//use Psr\Http\Message\ResponseInterface;
//use Laminas\Diactoros\Response\JsonResponse;

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
//    public function receiveAction()
//    {
//        $request = $this->getRequest();
//        $post = $request->getPost();
//        
//        $category = $post['value'];
//        $json = $this->getRequest()->getPost()['value'];
//        $result = json_decode($json, true);
//
//        $categoryRepository = $this->container->get(\Application\Model\CategoryRepositoryInterface::class);
//        if(! $categoryRepository instanceof Model\CategoryRepositoryInterface) {
//            throw new InvalidArgumentException('Wrong repository');
//        }
//        $returnCode = $categoryRepository->addCategories($result);
//        return new ViewModel(['returnCode' => $returnCode]);
//    }
    
    public function showProviderAction()
    {
        $id = $this->params()->fromRoute('id' , '1');
        $providerRepository = $this->container->get(\Application\Model\RepositoryInterface\ProviderRepositoryInterface::class);
        
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
        $repository = $this->container->get(\Application\Model\RepositoryInterface\ProviderRepositoryInterface::class);
        $request = $this->getRequest();
        $content = $request->getContent();

        if($request->isDelete()) {
            // Perform delete action
            $arr = $repository->delete($content);

            $response = $this->getResponse();

            $response->setStatusCode($arr['statusCode']);

            $answer = ['result' => $arr['result'], 'description' => $arr['description']];

            return new JsonModel($answer);
        }        
                
        $arr = $repository->replace($content);

        $response = $this->getResponse();
        
        $response->setStatusCode($arr['statusCode']);

        $answer = ['result' => $arr['result'], 'description' => $arr['description']];

        return new JsonModel($answer);
        
    }
    
    public function receiveStoreAction()
    {
        $repository = $this->container->get(\Application\Model\RepositoryInterface\StoreRepositoryInterface::class);
        $request = $this->getRequest();
        $content = $request->getContent();

        if($request->isDelete()) {
            // Perform delete action
            $arr = $repository->delete($content);

            $response = $this->getResponse();

            $response->setStatusCode($arr['statusCode']);

            $answer = ['result' => $arr['result'], 'description' => $arr['description']];

            return new JsonModel($answer);
        }        
        
        $arr = $repository->replace($content);

        $response = $this->getResponse();
        
        $response->setStatusCode($arr['statusCode']);

        $answer = ['result' => $arr['result'], 'description' => $arr['description']];

        return new JsonModel($answer);
        
    }
    
    public function receiveProductAction()
    {
        $repository = $this->container->get(\Application\Model\RepositoryInterface\ProductRepositoryInterface::class);
        $request = $this->getRequest();
        $content = $request->getContent();

        if($request->isDelete()) {
            // Perform delete action
            $arr = $repository->delete($content);
            
            $response = $this->getResponse();

            $response->setStatusCode($arr['statusCode']);

            $answer = ['result' => $arr['result'], 'description' => $arr['description']];

            return new JsonModel($answer);
        }        
        
        $arr = $repository->replace($content);

        $response = $this->getResponse();
        
        $response->setStatusCode($arr['statusCode']);

        $answer = ['result' => $arr['result'], 'description' => $arr['description']];

        return new JsonModel($answer);
    }

    public function receivePriceAction()
    {
        $repository = $this->container->get(\Application\Model\RepositoryInterface\PriceRepositoryInterface::class);
        $request = $this->getRequest();
        $content = $request->getContent();

        if($request->isDelete()) {
            $this->getResponse()->setStatusCode(405);
            return new JsonModel(['description' => 'Method is not supported: cannot delete price']);
        }
        
        $arr = $repository->replace($content);

        $response = $this->getResponse();
        
        $response->setStatusCode($arr['statusCode']);

        $answer = ['result' => $arr['result'], 'description' => $arr['description']];

        return new JsonModel($answer);
    }
    
    public function receiveStockBalanceAction()
    {
        $repository = $this->container->get(\Application\Model\RepositoryInterface\StockBalanceRepositoryInterface::class);
        $request = $this->getRequest();
        $content = $request->getContent();

        if($request->isDelete()) {
            // Perform delete action
            $this->getResponse()->setStatusCode(405);
            return new JsonModel(['description' => 'Method is not supported: cannot delete stock balance']);
        }

        $arr = $repository->replace($content);

        $response = $this->getResponse();
        
        $response->setStatusCode($arr['statusCode']);

        $answer = ['result' => $arr['result'], 'description' => $arr['description']];

        return new JsonModel($answer);
    }

    public function receiveCategoryAction()
    {
        $repository = $this->container->get(\Application\Model\RepositoryInterface\CategoryRepositoryInterface::class);
        $request = $this->getRequest();
        $content = $request->getContent();

        if($request->isDelete()) {
            // Perform delete action
            $arr = $repository->delete($content);
            return new JsonModel($arr);
        }

        //$content = '[{"title": "0001", "parent_id": "0001", "id": "0001", "description": "comment1 - moment1", "icon":"1234", "sort_order":10 }]';
        $arr = $repository->replace($content);

        $response = $this->getResponse();
        
        $response->setStatusCode($arr['statusCode']);

        $answer = ['result' => $arr['result'], 'description' => $arr['description']];

        return new JsonModel($answer);
    }

}
