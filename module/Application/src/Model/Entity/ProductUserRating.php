<?php

// src/Model/Entity/ProductUserRating.php

namespace Application\Model\Entity;

use Application\Model\Traits\Searchable;
use Application\Model\RepositoryInterface\ProductUserRatingRepositoryInterface;

/**
 * ProductUserRating
 */
class ProductUserRating extends Entity
{

    use Searchable;
    
    public static ProductUserRatingRepositoryInterface $repository;

    /**
     * @var string
     */
    protected $product_id;
     
    /**
     * @var string
     */
    protected $user_id;

    /**
     * @var string
     */
    protected $rating;
    
    /**
     * Get productId.
     *
     * @return string
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set product_id.
     *
     * @param string $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        $this->product_id = $productId;
       
        return $this;
    }

     /**
     * Set value.
     *
     * @param int $rating
     *
     * @return $this
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get value.
     *
     * @return string
     */
    public function getRating()
    {
        return $this->rating;
    }
     /**
     * Get id.
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set user_id.
     *
     * @param string $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
        return $this;
    }

}
