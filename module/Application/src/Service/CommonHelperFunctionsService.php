<?php

// src\Service\Factory\CommonHelperFunctionsService.php

namespace Application\Service;

use Laminas\Config\Config;
use Laminas\Json\Json;
use Laminas\Session\Container;
use Application\Resource\Resource;
use Application\Model\Entity\Basket;
use Application\Model\Entity\ClientOrder;
use Application\Model\Entity\Delivery;
use Application\Model\Entity\Provider;

//use Laminas\Session\Container;
//use Laminas\Json\Json;
//use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;

/**
 * Description of CommonHelperFunctionsService
 *
 * @author alex
 */
class CommonHelperFunctionsService
{

    /**
     * @var Config
     */
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function example()
    {
//        Basket::findAll([]);
//        ClientOrder::findAll([]);
//        Delivery::findAll([]);
    }

    /**
     * Update legal stores
     *
     * @param string $json
     * @return array
     */
    public function updateLegalStores($json)
    {

        $url = $this->config['parameters']['1c_request_links']['get_store'];
        $result = file_get_contents(
                $url,
                false,
                stream_context_create(['http' => ['method' => 'POST', 'header' => 'Content-type: application/json', 'content' => $json]])
        );
        if (!$result) {
            return ["result" => false, "error" => "1C не отвечает "];
        }

        $legalStore = Json::decode($result, true);

        foreach ($legalStore as $store) {
            $sessionLegalStore[$store['store_id']] = $store['delivery_speed_in_hours'];
            if ($store['time_until_closing']) {
                $store['time_until_closing'] += time();
            }
            $sessionLegalStoreArray[$store['store_id']] = $store;
        }
        $container = new Container(Resource::SESSION_NAMESPACE);
        $container->legalStore = $sessionLegalStore; //Json::decode($result, true);
        $container->legalStoreArray = $sessionLegalStoreArray;

        return ["result" => true, "message" => "Магазины получены"];
    }
    
    public function setErrorRedirect($errorCode)
    {
        $response = new Response();
        $response->setStatusCode($errorCode);
        return $response;
    }
    
    public function getProductCardJson($products)
    {
        if (empty($products)){
            return [];
        }
         $container = new Container(Resource::SESSION_NAMESPACE);
        //$return['legalStores'] = 
        $legalStores = $container->legalStore;
        foreach ($products as $product) {
            if (!isset($filteredProducts[$product->getId()])) {
                $oldPrice = 0;
                $price = $product->getPrice();
                $discont = $product->getDiscount();
                if ($discont > 0 ){
                    $oldPrice =  $price;
                    $price = $oldPrice - ($oldPrice * $discont /100);
                }
                //$provider = ;
                $strs = $product->getProvider()->getStores();
                $available = false; 
                $store =[];
                
                foreach($strs as $s){
                    if (!empty($legalStores[$s->getId()])) {
                        $available = true; 
                       $store[] =  $s->getId();
                       //break;
                    }
                }
                $return[$product->getId()] = [
                    "reserve" => $product->receiveRest($store),
                    "price" => $product->getPrice(),
                    'available' =>  $available,
                    //'store' =>  $store,
                    "oldprice" => $oldPrice,
                    "discount" => $product->getDiscount(),
                    "image" => $product->receiveFirstImageObject()->getHttpUrl(),
                ];
            }
        }
        return $return;
    }
    

}
