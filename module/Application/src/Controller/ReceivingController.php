<?php

// src/Controller/ReceivingController.php

declare(strict_types=1);

namespace Application\Controller;

use Application\Model;
use Interop\Container\ContainerInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;

//use Laminas\Json\Json;
//use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;

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
     * Receives repository updates depending on the route
     * @return JsonModel
     */
    public function receiveRepositoryAction()
    {

        $routeMatch = $this->getEvent()->getRouteMatch();

        $routeName = $routeMatch->getMatchedRouteName();

        //$params = $routeMatch->getParams();

        $config = $this->container->get('Config');

        $repository = $this->container->get($config['router']['routes'][$routeName]['options']['repository']);

        $request = $this->getRequest();

        $content = $request->getContent();

        if ($request->isDelete()) {
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

}
