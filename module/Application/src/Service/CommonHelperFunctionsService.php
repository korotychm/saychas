<?php

// src\Service\Factory\CommonHelperFunctionsService.php

namespace Application\Service;

use Laminas\Config\Config;
use Laminas\Json\Json;
use Laminas\Session\Container;
use Application\Resource\Resource;
use Application\Model\Entity\ProductFavorites;
use Application\Model\Entity\Basket;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Application\Helper\MathHelper;

/**
 * Description of CommonHelperFunctionsService
 *
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

    /**
     * Update legal stores
     *
     * @param string $json
     * @return array
     */
    public function updateLegalStores($json)
    {
        $url = $this->config['parameters']['1c_request_links']['get_store'];
        
        
        try {
            $result = file_get_contents($url, false, stream_context_create(['http' => ['method' => 'POST', 'header' => 'Content-type: application/json', 'content' => $json]]));
        } catch (\Exception $e) {
            return ["result" => false, "error" => "server time out. $e"];
        }
        
        if (empty($result)) {
            return ["result" => false, "error" => "server time out"];
        }
        
        $legalStore = Json::decode($result, Json::TYPE_ARRAY);

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

    /**
     * 
     * @param int $errorCode
     * @return Response
     */
    public function setErrorRedirect($errorCode) 
    {
        $response = new Response();
        $response->setStatusCode($errorCode);
        return $response;
    }

    /**
     * 
     * @param object $products
     * @param int $userId
     * @return array
     */
    public function getProductCardArray($products, $userId, $count = null, $limit = null )
    {
        if (empty($products)) {
            return [];
        }
       
        $container = new Container(Resource::SESSION_NAMESPACE);
        $legalStores = $container->legalStore;
        
        foreach ($products as $product) {
            $item = ['title' => $product->getTitle(),  'discount' => $product->getDiscount(), 'isFav' => $this->isInFavorites($product->getId(), $userId)];
    
            $item['oldPrice'] = $product->getPrice();
            $item['price'] = MathHelper::roundRealPrice($item['oldPrice'], $item['discount']); 
            
            $strs = $product->getProvider()->getStores();
            $item['available'] = false;
            $store = [];
            
            foreach ($strs as $s) {
                if (!empty($legalStores[$s->getId()])) {
                    $item['available'] = true;
                    $store[] = $s->getId();
                }
            }
            
            $image = $product->receiveFirstImageObject();
            $item['image'] = (!empty($image)) ? $image->getHttpUrl() : null; 
            $item['reserve'] = $product->receiveRest($store);
            $item['rating'] = $product->getRating();
            $item['reviews'] = $product->getReviews();
            $item['url'] = $product->getUrl();
            $return[$product->getId()] = $item;
       }
       //$return["count"] = $count; 
       return ["count" => $count,  "limit" => $limit,  "products" => $return ?? []];
    }

    /**
     * 
     * @param object $user
     * @return array
     */
    public function getUserInfo($user)
    {
        if (empty($user)) {
            return [];
        }
       
        $return = [];
        $return['id'] = $user->getId();
        $return['userid'] = $user->getUserId();
        $return['name'] = $user->getName();
        $return['phone'] = $user->getPhone();
        $return['email'] = $user->getEmail();
        $usdat = $user->getUserData()->current();
        
        if (null != $usdat) {
            $return['userAddress'] = $usdat->getAddress();
            $return['userGeodata'] = $usdat->getGeoData();
        }
        
        return $return;
    }

    /**
     * 
     * @param string $productId
     * @param int $userId
     * @return boolean
     */
    public function isInFavorites($productId, $userId)
    {
        if (empty($userId)) {
            return false;
        }
       
        return !empty(ProductFavorites::find(['user_id' => $userId, 'product_id' => $productId]));
    }

    /**
     * Count basket product
     * 
     * @param int $userId
     * @return int
     */
    public function basketProductsCount($userId)
    {
       return Basket::findAll(["where" => ["user_id" => $userId, "order_id" => 0], 'columns' => ["user_id"]])->count();
    }

}
