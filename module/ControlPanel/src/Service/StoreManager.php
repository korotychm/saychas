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

    /**
     * Find all stores
     * 
     * @param array $params
     * @return array
     */
    public function findAll($params)
    {
        if (isset($params['pageNo'])) {
            $limits = $this->calcLimits($params['pageNo']);
            $collection = $this->db->{$this->collectionName};
            $c = $collection->count($params['where']);

            $cursor = $collection->find(
            $params['where'],
            [
                'skip' => $limits['min'] - 1,
                'limit' => $this->pageSize,
//                'projection' => [
//                    'id' => 1,
//                    'title' => 1,
//                    'description' => 1,
//                    'status_id' => 1,
//                    'status_name' => 1,
//                    'address' => 1,
//                    'provider_id' => 1,
//                    '_id' => 0
//                ],
            ]);
            $result['body'] = $cursor->toArray();
            $result['limits'] = $limits;
            $result['limits']['total'] = $this->calcLimits($params['pageNo'], $c)['total'];

            return $result;
        }
        return [];
    }

    /**
     * Find store statuses
     * 
     * @return array
     */
    private function findStatuses1()
    {
        $collectionName = 'store_statuses';
        $collection = $this->db->{$collectionName};
        $results = $collection->find([], ['_id' => 0, 'status_id' => 1, 'status_name' => 1])->toArray();
        $result = [];
        foreach ($results as $c) {
            $result[] = [$c['status_id'], $c['status_name']];
        }
        return $result;
    }
    
    /**
     * Find store statuses in config
     * 
     * @return array
     */
    private function findStatuses()
    {
        return $this->config['parameters']['store_statuses'];
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

        $cursor['filters']['statuses'] = $this->findStatuses();

        return $cursor;
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

