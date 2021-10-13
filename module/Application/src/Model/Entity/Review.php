<?php

// src/Model/Entity/Review.php

namespace Application\Model\Entity;

use Application\Model\Repository\ReviewRepository;
use Application\Model\Traits\Searchable;

/**
 * Review
 *
 * @ORM\Table(name="review")
 * @ORM\Entity
 */
class Review extends Entity
{

    /**
     * Behavior
     */
    use Searchable;

    /**
     * @var ReviewRepository
     */
    public static ReviewRepository $repository;

    public function autoIncrementKey(): string
    {
        return 'id';
    }
    /**
     * Get primary key
     *
     * @return string|array
     */
    public function primaryKey()
    {
        return ['id'];
    }
    
    /**
     * @var int
     */
    protected $id = 0;
    
    /**
     * @var string
     */
    protected $review_id ;
    
    /**
     * @var string
     */
    protected $user_id ;

    /**
     * @var string
     */
    protected $product_id;

    /**
     * @var int
     */
    protected $rating = 0;

    /**
     * @var string
     */
    protected $user_name ;

    /**
     * @var string
     */
    protected $seller_name ;

    /**
     * @var string
     */
    protected $user_message ;

    /**
     * @var string
     */
    protected $seller_message;

    /**
     * @var int
     */
    protected $time_created = 0;

    
    /**
     * @var int
     */
    protected $time_modified = 0;
    
    /**
     * @var timestamp
     */
    protected $timestamp;

    
    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get review_id.
     *
     * @return string
     */
    public function getReviewId()
    {
        return $this->review_id;
    }

    /**
     * Set review_id.
     *
     * @param string $reviewId
     * @return $this
     */
    public function setReviewId($reviewId)
    {
        $this->review_id = $reviewId;
        return $this;
    }

    /**
     * Get user_id.
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

    /**
     * Set product_id.
     *
     * @param string $productId
     *
     * @return Review
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
     * Set rating.
     *
     * @param string $rating
     * @return Review
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set user_name
     *
     * @param string $userName
     *
     * @return Review
     */
    public function setUserName($userName)
    {
        $this->user_name = $userName;

        return $this;
    }

    /**
     * Get user_name.
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->user_name;
    }


    /**
     * Set seller_name
     *
     * @param string $sellerName
     *
     * @return Review
     */
    public function setSellerName($sellerName)
    {
        $this->seller_name = $sellerName;

        return $this;
    }

    /**
     * Get seller_name.
     *
     * @return string
     */
    public function getSellerName()
    {
        return $this->seller_name;
    }
  
    /**
     * Set seller_message
     *
     * @param string sellerMessage
     *
     * @return Review
     */
    public function setSellerMessage($sellerMessage)
    {
        $this->seller_message = $sellerMessage;

        return $this;
    }

    /**
     * Get seller_message.
     *
     * @return string
     */
    public function getSellerMessage()
    {
        return $this->seller_message;
    }
    
    /** Set user_message
     *
     * @param string userMessage
     *
     * @return Review
     */
    public function setUserMessage($userMessage)
    {
        $this->user_message = $userMessage;

        return $this;
    }

    /**
     * Get user_message.
     *
     * @return string
     */
    public function getUserMessage()
    {
        return $this->user_message;
    }

    /**
     * Get time_created
     *
     * @return string
     */
    public function getTimeCreated()
    {
        return $this->time_created;
    }

    /**
     * Set time_created
     *
     * @param string $timeCreated
     * @return $this
     */
    public function setTimeCreated($timeCreated)
    {
        $this->time_created = $timeCreated;
        return $this;
    }
    
    /**
     * Get time_modified
     *
     * @return string
     */
    public function getTimeModified()
    {
        return $this->time_modified;
    }

    /**
     * Set time_modified
     *
     * @param string $timeModified
     * @return $this
     */
    public function setTimeModified($timeModified)
    {
        $this->time_modified = $timeModified;
        return $this;
    }
    
    /**
     * Get timestamp
     *
     * @return timestamp
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
    
    /**
     * Set timestamp
     *
     * @param $timestamp
     * @return $this
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }
    
}
