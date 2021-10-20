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
        $result = file_get_contents($url, false, stream_context_create(['http' => ['method' => 'POST', 'header' => 'Content-type: application/json', 'content' => $json]]));
        
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
    public function getProductCardArray($products, $userId, $count = null )
    {
        if (empty($products)) {
            return [];
        }
       
        $container = new Container(Resource::SESSION_NAMESPACE);
        $legalStores = $container->legalStore;
        
        foreach ($products as $product) {
            $item = ['title' => $product->getTitle(), 'oldPrice' => 0, 'price' => $product->getPrice(), 'discont' => $product->getDiscount(), 'isFav' => $this->isInFavorites($product->getId(), $userId)];
        
            if ($item['discont'] > 0) {
                $item['oldPrice'] = $item['price'];
                $item['price'] = $item['oldPrice'] - ($item['oldPrice'] * $item['discont'] / 100);
            }
            
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
            $item['image'] = (!empty($image)) ? $image->getHttpUrl() : null; //Resource::DEFAULT_IMAGE;
            $item['reserve'] = $product->receiveRest($store);
            $item['rating'] = $product->getRating();
            $item['reviews'] = $product->getReviews();
            $return[$product->getId()] = $item;
       }
       //$return["count"] = $count;
       return ["count" => $count, "products" => $return];
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
