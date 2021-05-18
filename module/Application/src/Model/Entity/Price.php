<?php

// src/Model/Entity/Price.php

namespace Application\Model\Entity;

//use Doctrine\ORM\Mapping as ORM;

/**
 * Price
 *
 * @ORM\Table(name="price", uniqueConstraints={@ORM\UniqueConstraint(name="id_product", columns={"product_id", "store_id"})})
 * @ORM\Entity
 */
class Price extends Entity
{

    /**
     * @var string
     */
    protected $productId;

    /**
     * @var int
     */
    protected $reserve;

    /**
     * @var string
     */
    protected $storeId;

    /**
     * @var string
     */
    protected $unit;

    /**
     * @var int
     */
    protected $price;
    
    /**
     * @var int
     */
    protected $old_price;

    /**
     * @var string
     */
    protected  $providerId;

    /**
     * Set productId.
     *
     * @param int $productId
     *
     * @return Price
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId.
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set reserve.
     *
     * @param int $reserve
     *
     * @return Price
     */
    public function setReserve($reserve)
    {
        $this->reserve = $reserve;

        return $this;
    }

    /**
     * Get reserve.
     *
     * @return int
     */
    public function getReserve()
    {
        return $this->reserve;
    }

    /**
     * Set storeId.
     *
     * @param int $storeId
     *
     * @return Price
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;

        return $this;
    }

    /**
     * Get storeId.
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * Set unit.
     *
     * @param string $unit
     *
     * @return Price
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit.
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set providerId.
     *
     * @param string $providerId
     *
     * @return Price
     */
    public function setProviderId($providerId)
    {
        $this->providerId = $providerId;

        return $this;
    }

    /**
     * Get providerId.
     *
     * @return string
     */
    public function getProviderId()
    {
        return $this->providerId;
    }

    /**
     * Set price.
     *
     * @param int $price
     *
     * @return Price
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }
    
    /**
     * Set old_price.
     * 
     * @param int $oldPrice
     * @return $this
     */
    public function setOldPrice($oldPrice)
    {
        $this->old_price = $oldPrice;
        return $this;
    }
    
    /**
     * Get old_price
     * 
     * @return int
     */
    public function getOldPrice()
    {
        return $this->old_price;
    }

}
