<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Json\Json;
use Laminas\View\Model\JsonModel;
//use Laminas\Session\Container; // as SessionContainer;
use Application\Service\Factory\DadataServiceFactory;
use Laminas\Authentication\AuthenticationService;
use Application\Resource\Resource;
use Application\Helper\ArrayHelper;
//use Laminas\Escaper\Escaper;
use Application\Service\DadataService;
//use Application\Service\ImageHelperFunctionsService;
//use Application\Service\ExternalCommunicationService;
//use Application\Model\Entity\Setting;
//use Application\Model\Entity\Review;
//use Application\Model\Entity\ReviewImage;
//use Application\Model\Entity\User;
//use Application\Model\Entity\ProductRating;
//use Application\Model\RepositoryInterface\ProductRepositoryInterface;

//use Laminas\Filter\StripTags;


class DadataController extends AbstractActionController
{

    //privat
    private $config;
    
    public function __construct($config, AuthenticationService $authService,  DadataService $dadata)
    {
        $this->config = $config;
        $this->authService = $authService;
        $this->dadata = $dadata;
    }
    
    public function getDadataHintsAction ()
    {
        if (empty($userId = $this->identity())) {
            return $this->getResponse()->setStatusCode(403);
        }
        $dadataApiParams = $this->config['parameters']['dadataApiParams'];
        $post = $this->getRequest()->getPost();
        $address = $post->address ?? "Москва, Угрешская, 1";
        $limit = $post->limit  ??  $dadataApiParams['limit'];
        $dadata = new DadataService($dadataApiParams['url'], $dadataApiParams['token'], $dadataApiParams['secret']);
        $answer = $dadata->clean(["query" => $address, "count" => $limit ]) ?? "[]";
        
            try {
                $return = array_merge(["result" =>true], Json::decode($answer, Json::TYPE_ARRAY));
            } catch (\Throwable $ex) {
                
                return new JsonModel(["result" => false, 'error' => $ex->getMessage(), ["query" => $address, "count" => $limit ]]);
            }
        
            return new JsonModel($return);
    }
    

}
