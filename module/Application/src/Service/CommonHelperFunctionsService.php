<?php

// src\Service\Factory\CommonHelperFunctionsService.php

namespace Application\Service;
use Application\Model\Entity;
use Laminas\Config\Config;
use Laminas\Json\Json;
//use Laminas\View\Model\ViewModel;
use Laminas\Session\Container;
use Application\Resource\Resource;
use Application\Model\Entity\ProductFavorites;
/*use Application\Model\Entity\Basket;
use Application\Model\Entity\ClientOrder;
use Application\Model\Entity\Delivery;
use Application\Model\Entity\Provider;
use Application\Model\Entity\Product;*/
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;

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

    public function __construct($config, 
            HandbookRelatedProductRepositoryInterface $productRepository)
    {
        $this->config = $config;
        $this->productRepository = $productRepository;
    }

    public function example()
    {
          ProductFavorites::findAll([]);
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
        $result = file_get_contents($url, false, stream_context_create(['http' => ['method' => 'POST', 'header' => 'Content-type: application/json', 'content' => $json]]));
        if (!$result) {
            return ["result" => false, "error" => "server time out"];
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
        
        return ["result" => true, "message" => "Stores received"];
    }

    public function setErrorRedirect($errorCode)
    {
        $response = new Response();
        $response->setStatusCode($errorCode);
        return $response;
    }
    
    public function getProductCardArray($products, $userId)
    {
        if (empty($products)){
            return [];
        }
        $container = new Container(Resource::SESSION_NAMESPACE);
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
                    "title" => $product->getTitle(),

                    'available' =>  $available,

                    //'store' => $product->getStoreId(),

                    "oldprice" => $oldPrice,
                    "discount" => $product->getDiscount(),
                    "image" => $product->receiveFirstImageObject()->getHttpUrl(),
                    'isFav' => $this->isInFavorites($product->getId(), $userId ),
                ];
            }
        }
        return $return;
    }

    public function getUserInfo($user)
    {
        if (null == $user) {
            return [];
        }
        //$container = new Container(Resource::SESSION_NAMESPACE);
        $return['id'] = $user->getId();
        $return['userid'] = $user->getUserId();
        $return['name'] = $user->getName();
        $return['phone'] = $user->getPhone();
        $return['email'] = $user->getEmail();
        $userData = $user->getUserData();
        $usdat = $userData->current();
        if (null != $usdat) {
            $return['userAddress'] = $usdat->getAddress(); //$container->userAddress;
            $return['userGeodata'] = $usdat->getGeoData();
        }
        return $return;
    }
    
    public function isInFavorites ($productId, $userId)
    {
        if (!empty($userId)) {
            if (!empty(ProductFavorites::find(['user_id' => $userId, 'product_id' => $productId]))){
                return true;
            }
        }
        return  false; 
    }

    
    
 
}
