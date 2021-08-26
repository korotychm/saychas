<?php

// src/Model/Traits/Collection.php

namespace ControlPanel\Model\Traits;

/**
 * Description of Collection
 *
 * @author alex
 */
trait Collection
{
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
    protected function countCollection($collectionName = self::COLLECTION_NAME)
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
        
        $this->collectionSize = $this->countCollection();

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
    
    public function findAll($params)
    {
        
    }

}
