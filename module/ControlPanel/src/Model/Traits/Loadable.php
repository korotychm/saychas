<?php

// src/Model/Traits/Loadable.php

namespace ControlPanel\Model\Traits;

/**
 * Description of Collection
 *
 * @author alex
 */
trait Loadable
{
    /**
     * @var string
     */
    //protected $collectionName = self::COLLECTION_NAME;
    
    /**
     * @var \MongoDB\Client
     */
    protected $mclient;

    /**
     * @var db
     */
    protected $db;

    /**
     * Rows per page
     * 
     * @var int
     */
    protected $pageSize;// = 3;

    /**
     * @var int
     */
    protected $collectionSize;
    
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
     * Delete documents from $collectionName identified by provider_id(partner_id)
     * 
     * @param string $collectionName
     * @param array $params
     * @return MongoObject
     */
    protected function deleteMany(string $collectionName, array $params = [])
    {
        $collection = $this->db->$collectionName;
        $deleteResult = $collection->deleteMany($params);
        return $deleteResult;
    }
    
    private function extractCredentials(array $credentials) : array
    {
        $result = [];
        foreach ($credentials as $c) {
            list($key, $value) = explode(':', $c);
            $result[trim($key)] = trim($value);
        }
        
        return $result;
    }

    /**
     * Insert array of documents into collection
     *
     * @param string $collectionName
     * @param array $documents
     * @return type
     */
    protected function insertManyInto(/*$collectionName,*/ array $documents)
    {
        $collection = $this->db->{$this->collectionName};
        return $collection->insertMany($documents);
    }

    /**
     * Count documents in a collection
     *
     * @param string $collectionName
     * @return int
     */
    protected function countCollection(/*$collectionName = self::COLLECTION_NAME*/)
    {
        $collection = $this->db->{$this->collectionName};
        return $collection->count();
    }

    /**
     * Set product number per page
     *
     * @param int $pageSize
     * @return $this
     */
    public function setPageSize(int $pageSize)
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
    public function calcLimits(int $pageNumber, $collectionSize = -1) : array
    {
        $limits = ['min' => 0, 'max' => 0, 'total' => 0];
        
        $this->collectionSize = $collectionSize;
        if($collectionSize < 0) {
            $this->collectionSize = $this->countCollection();
        }

        if (0 < $this->collectionSize) {
            $div = (int) ($this->collectionSize / $this->pageSize);
            $mod = $this->collectionSize % $this->pageSize;
            //if(0 == $mod) { $pageNumber -= 1; }
            $limits['min'] = ($pageNumber - 1) * $this->pageSize + 1;
            $limits['min'] = ($limits['min'] > $div * $this->pageSize + $mod) ? $div * $this->pageSize + 1 : $limits['min'];
            $limits['max'] = $pageNumber * $this->pageSize;
            $limits['max'] = ($limits['max'] > $this->collectionSize ? $this->collectionSize : $limits['max']);
            $limits['total'] = $div + (0 == $mod ? 0 : 1);
        }

        return $limits;
    }

    /**
     * Load all json array of objects from 1C for logged in user and store it into db
     * 
     * @param string $url
     * @param array $credentials
     * @return array
     */
    public function loadAll(string $url, array $credentials = []) : array
    {
        $answer = $this->curlRequestManager->sendCurlRequestWithCredentials($url, [], $credentials);

        //$this->dropCollection(self::COLLECTION_NAME);
        
        $cred = $this->extractCredentials($credentials);
        
        $this->deleteMany($this->collectionName/*self::COLLECTION_NAME*/, ['provider_id' => $cred['partner_id']]);

        $this->insertManyInto(/*$this->collectionName*/ /*self::COLLECTION_NAME,*/ $answer['data']);

        $this->collectionSize = $this->countCollection();
        
        return $answer;
    }
    
    /**
     * Find all in collection
     * 
     * @param array $params
     * @return array
     */
    public function findAll(array $params) : array
    {
        if(!isset($params['columns'])) {
            $params['columns'] = [];
        }
        if (isset($params['pageNo'])) {
            $limits = $this->calcLimits($params['pageNo']);
            $collection = $this->db->{$this->collectionName};
            $c = $collection->count($params['where']);

            $cursor = $collection->find(
            $params['where'],
            [
                'skip' => $limits['min'] - 1,
                'limit' => $this->pageSize,
                'projection' => $params['columns'],
            ]);
            $result['body'] = $cursor->toArray();
            $result['limits'] = $limits;
            $result['limits']['total'] = $this->calcLimits($params['pageNo'], $c)['total'];

            return $result;
        }
        return [];
    }
    
    public function find(array $params)
    {
        $collection = $this->db->{$this->collectionName};
        $document = $collection->findOne($params);
        if(null == $document) {
            throw \Exception('Product cannot be null');
        }
        return $document;
    }
    

}
