<?php

// src/Model/Entity/ProductHistory.php

namespace Application\Model\Entity;

use Application\Model\Repository\ProductHistoryRepository;
use Application\Model\Traits\Searchable;

/**
 * ProductHistory
 *
 * @ORM\Table(name="product_history")
 * @ORM\Entity
 */
class ProductHistory extends Entity
{

    /**
     * Behavior
     */
    use Searchable;

    /**
     * @var ProductHistoryRepository
     */
    public static ProductHistoryRepository $repository;

    /**
     * @var int
     */
    protected $user_id = 0;

    /**
     * @var string
     */
    protected $product_id;

    /**
     * @var timestamp
     */
    protected $timestamp;
    
    /**
     * @var  int  
     * Unix time
     */
    protected $time;

    /**
     * Get user_id.
     *
     * @return string
     */
    
    public function primaryKey()
    {
        return ['user_id', 'product_id'];
    }
    
    
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set user_id.
     *
     * @param string $userId
     * @return ProductHistory
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
        return $this;
    }

    /**
     * Set product_id.
     *
     * @param string $productId
     *
     * @return ProductHistory
     */
    public function setProductId($productId)
    {
        $this->product_id = $productId;

        return $this;
    }

    /**
     * Get product_id.
     *
     * @return string
     */
    public function getProductId()
    {
        return $this->product_id;
    }
    
    
    /**
     * Receive timestamp
     * 
     * @return timestamp
     */
    public function receiveTimestamp()
    {
        return $this->timestamp;
    }

    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }
    
    public function getTime()
    {
        return $this->time;
    }
    
    
    /**
     * Get ts
     * 
     * @return timestamp
     */
//    public function getTs()
//    {
//        return $this->ts;
//    }
    
    /**
     * Get ts
     * 
     * @param type $ts
     * @return ProductHistory
     */
//    public function setTs($ts)
//    {
//        $this->ts = $ts;
//        return $this;
//    }
    

}
