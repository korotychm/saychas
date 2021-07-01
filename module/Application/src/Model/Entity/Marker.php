<?php

// src/Model/Entity/Marker.php

namespace Application\Model\Entity;

//use Doctrine\ORM\Mapping as ORM;

/**
 * Marker
 *
 * @ORM\Table(name="marker")
 * @ORM\Entity
 */
class Marker extends Entity
{

    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $product_id;

    /**
     * @var int
     */
    protected $marker_index;
    
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
     * Get id.
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
     * Set marker_index.
     *
     * @param int $markerIndex
     *
     * @return Marker
     */
    public function setMarkerIndex($markerIndex)
    {
        $this->marker_index = $markerIndex;

        return $this;
    }

    /**
     * Get marker_index.
     *
     * @return int
     */
    public function getMarkerIndex()
    {
        return $this->marker_index;
    }

}
