<?php

// ControlPanel\src\Service\PriceAndDiscountManager.php

namespace ControlPanel\Service;

use ControlPanel\Service\CurlRequestManager;
//use ControlPanel\Model\Traits\Loadable;
use ControlPanel\Contract\LoadableInterface;
//use Application\Model\Repository\CategoryRepository;

/**
 * Description of PriceAndDiscountManager
 *
 * @author alex
 */
class PriceAndDiscountManager extends ListManager implements LoadableInterface
{

//    use Loadable;

    protected $collectionName = 'price_and_discount';

    /**
     * @var string
     */
    protected $dbName = 'saychas_cache';

    /**
     * @var Laminas\Config\Config
     */
    protected $config;

    /**
     * @var CurlRequestManager
     */
    protected $curlRequestManager;

    /**
     * Constructor
     *
     * @param Laminas\Config\Config $config
     * @param CurlRequestManager $curlRequestManager
     * @param \MongoDB\Client $mclient
     */
    public function __construct($config, CurlRequestManager $curlRequestManager, \MongoDB\Client $mclient)
    {
        $this->config = $config;
        $this->curlRequestManager = $curlRequestManager;
        $this->mclient = $mclient;
        $this->db = $this->mclient->{$this->dbName};
    }

    /**
     * Find store documents for specified provider
     * 
     * @param array $params
     * @return array
     */
    public function findDocuments($params)
    {
        $cursor = $this->findAll($params);

        return $cursor;
    }

    public function updateServerDocument($headers, $content = [])
    {
        $url = $this->config['parameters']['1c_provider_links']['lk_update_price_and_discount'];
        $result = $this->curlRequestManager->sendCurlRequestWithCredentials($url, $content, $headers);
        return $result;
    }
    
    public function addServerDocument($headers, $content = [])
    {
        $url = $this->config['parameters']['1c_provider_links']['lk_add_price_and_discount'];
        $result = $this->curlRequestManager->sendCurlRequestWithCredentials($url, $content, $headers);
        return $result;
    }
    
    public function replacePriceAndDiscount($priceAndDiscount)
    {
        $collection = $this->db->{$this->collectionName};
        $collection->deleteMany([
            'id' => $priceAndDiscount['id'],
            'provider_id' => $priceAndDiscount['provider_id'],
        ]);
        $updateResult = $collection->insertOne($priceAndDiscount);
        return $updateResult;
    }
    
    
}

