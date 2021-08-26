<?php

// ControlPanel\src\Service\StoreManager.php

namespace ControlPanel\Service;

use ControlPanel\Service\CurlRequestManager;
use ControlPanel\Model\Traits\Loadable;

/**
 * Description of StoreManager
 *
 * @author alex
 */
class StoreManager
{

    use Loadable;

    public const COLLECTION_NAME = 'stores';

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

    public function findAll($params)
    {
        if (isset($params['pageNo'])) {
            $limits = $this->calcLimits($params['pageNo']);
            $collection = $this->db->{$this->collectionName};
            $cursor = $collection->find([
                    ], [
                'skip' => $limits['min'] - 1,
                'limit' => $this->pageSize,
                'projection' => [
                    'id' => 1,
                    'title' => 1,
                    'description' => 1,
                    'provider_id' => 1,
                    '_id' => 0
                ],
            ]);
            return $cursor->toArray();
        }
        return [];
    }

}

//    protected $mclient;








//
//    /**
//     * @var string
//     */
//    protected $collectionName = self::COLLECTION_NAME;











//    public function loadAll($url, array $credentials = [])
//    {
////        $url = $this->config['parameters']['1c_provider_links']['lk_store_info'];
//
//        $answer = $this->curlRequestManager->sendCurlRequestWithCredentials($url, [], $credentials);
//
//        $this->dropCollection(self::COLLECTION_NAME);
//
//        $this->insertManyInto(self::COLLECTION_NAME, $answer['data']);
//
//        $this->collectionSize = $this->countCollection();
//
//        return $answer;
//        //return ['store1', 'store2', 'store3', 'store4',];
//    }

