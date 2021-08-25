<?php

// ControlPanel\src\Service\ProductManager.php

namespace ControlPanel\Service;

use Laminas\Hydrator\ClassMethodsHydrator;
use ControlPanel\Service\CurlRequestManager;

/**
 * Description of ProductManager
 *
 * @author alex
 */
class ProductManager
{

    /**
     * @var Laminas\Config\Config
     */
    protected $config;

    /**
     * @var CurlRequestManager
     */
    protected $curlRequestManager;

    /**
     * @var \MongoDB\Client
     */
    protected $mclient;

    /**
     * @var string
     */
    protected $dbName = 'saychas_cache';

    /**
     * @var db
     */
    protected $db;

    /**
     * @var int
     */
    protected $pageSize = 3;

    /**
     * @var int
     */
    protected $collectionSize;

    /**
     * Constructor
     *
     * @param Config $config
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
     * Drop collection from db
     *
     * @param $collection
     * @return result
     */
    protected function dropCollection($collection)
    {
        $result = $this->db->dropCollection($collection);
        return $result;
    }

    /**
     * Insert array of documents into collection
     *
     * @param string $collectionName
     * @param array $documents
     * @return type
     */
    protected function insertManyInto($collectionName, array $documents)
    {
        $collection = $this->db->$collectionName;
        return $collection->insertMany($documents);
    }

    /**
     * Count documents in a collection
     *
     * @param string $collectionName
     * @return int
     */
    protected function countCollection($collectionName)
    {
        $collection = $this->db->$collectionName;
        return $collection->count();
    }

    /**
     * Set product number per page
     *
     * @param type $pageSize
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * Calculate max and min values
     * in the product array for page with pageNumber
     *
     * @param int $pageNumber
     * @return array
     */
    public function calcLimits($pageNumber)
    {
        $limits = ['min' => 0, 'max' => 0];

        if (0 < $this->collectionSize) {
            $div = (int) ($this->collectionSize / $this->pageSize);
            $mod = $this->collectionSize % $this->pageSize;
            $limits['min'] = ($pageNumber - 1) * $this->pageSize + 1;
            $limits['min'] = ($limits['min'] > $div * $this->pageSize + $mod) ? $div * $this->pageSize + 1 : $limits['min'];
            $limits['max'] = $pageNumber * $this->pageSize;
            $limits['max'] = ($limits['max'] > $this->collectionSize ? $this->collectionSize : $limits['max']);
        }

        return $limits;
    }
    
    public function getAll()
    {
        
    }

    /**
     * Load all products from 1C for logged in user and store them into db
     *
     * @param array $credentials
     * @return array
     */
    public function loadAll(array $credentials = [])
    {
        $url = $this->config['parameters']['1c_provider_links']['lk_product_info'];

        $answer = $this->curlRequestManager->requestProducts($url, [], $credentials);

        $this->dropCollection('products');

        $this->insertManyInto('products', $answer['data']);

        $this->collectionSize = $this->countCollection('products');

        //$this->calcLimits(2);

        return $answer;
    }

}

//        $collection = $this->mclient->saychas_cache->profile;
//        $document = $collection->findOne();
//        var_dump( $document );
//        exit;






//        $this->dropCollection('banzaii');
//        $this->insertManyInto('banzaii', [['name' => 'name1', 'value' => 'value1'], ['name' => 'name2', 'value' => 'value2']]);
//        $count = $this->countCollection('banzaii');








        //$mongoProfile = $this->mclient->selectDatabase('saychas_cache')->selectCollection('profile')->findOne();

