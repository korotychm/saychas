<?php

// src/Model/Entity/ReviewImage.php

namespace Application\Model\Entity;

use Application\Model\Repository\ReviewImageRepository;
use Application\Model\Traits\Searchable;

/**
 * ReviewImage
 *
 * @ORM\Table(name="review_image")
 * @ORM\Entity
 */
class ReviewImage extends Entity
{

    /**
     * Behavior
     */
    use Searchable;

    /**
     * @var ReviewImageRepository
     */
    public static ReviewImageRepository $repository;

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
        return ['user_id', 'product_id', 'order_id'];
    }
    
    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @var int
     */
    protected $rewiew_id;

    /**
     * @var string
     */
    protected $filename;

    
    /**
     * Get id.
     *
     * @return string
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
     * Set review_id.
     *
     * @param string $reviewId
     *
     * @return ReviewImage
     */
    public function setReviewId($reviewId)
    {
        $this->review_id = $reviewId;

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
     * Set filename.
     *
     * @param string $filename
     * @return ReviewImage
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

}
