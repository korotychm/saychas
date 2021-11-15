<?php

// src/Controller/DadataController.php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Json\Json;
use Laminas\View\Model\JsonModel;
use Laminas\Authentication\AuthenticationService;
use Application\Service\DadataService;
use Laminas\Filter\StripTags;

class DadataController extends AbstractActionController
{

    private $config;

    public function __construct($config, AuthenticationService $authService, DadataService $dadata)
    {
        $this->config = $config;
        $this->authService = $authService;
        $this->dadata = $dadata;
    }

    /**
     * @route /dadata/get-hints
     * 
     * @return JsonModel
     */
    public function getDadataHintsAction()
    {
//        if (empty($userId = $this->identity())) {
//            return $this->getResponse()->setStatusCode(403);
//        }
        $json = file_get_contents('php://input');
         try {
            $post = Json::decode($json);
        } catch (\Throwable $ex) {

            return new JsonModel(["result" => false, 'error' => $ex->getMessage(), [$json]]);
        }
        
        $striptags = new StripTags();
        $address = $striptags->filter($post->query) ?? "";
//        if (strlen($address) < 3) {
//            return new JsonModel(["result" => false, 'error' => "address is very short"]);
//        }
        $dadataApiParams = $this->config['parameters']['dadataApiParams'];
        $limit = $post->count ?? $dadataApiParams['limit'];
        $dadata = new DadataService($dadataApiParams['url'], $dadataApiParams['token'], $dadataApiParams['secret']);
        $answer = $dadata->getDadata(["query" => $address, "count" => $limit]) ?? Json::encode(["result" => false, "error" => "dadata server not response" ]);
        $return = array_merge(["result" => true], Json::decode($answer, Json::TYPE_ARRAY));

//        try {
//            $return = array_merge(["result" => true], Json::decode($answer, Json::TYPE_ARRAY));
//        } catch (\Throwable $ex) {
//
//            return new JsonModel(["result" => false, 'error' => $ex->getMessage(), ["query" => $address, "count" => $limit]]);
//        }

        return new JsonModel($return);
    }

}
