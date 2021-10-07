<?php

// src/Model/Entity/ProductRating.php

namespace Application\Model\Entity;

use Application\Model\Traits\Searchable;
use Application\Model\RepositoryInterface\ProductRatingRepositoryInterface;

/**
 * ProductRating
 */
class ProductRating extends Entity
{

    use Searchable;
    
    public static ProductRatingRepositoryInterface $repository;

    /**
     * @var string
     */
    protected $product_id;
    
    /**
     * @var int
     */
    protected $reviews;

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
     * Get reviews
     *
     * @return int
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Set reviews.
     *
     * @param string $reviews
     * @return $this
     */
    public function setUserId($reviews)
    {
        $this->reviews = $reviews;
        return $this;
    }

}
