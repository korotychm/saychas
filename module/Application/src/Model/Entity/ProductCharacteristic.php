<?php

// src/Model/Entity/ProductCharacteristic.php

namespace Application\Model\Entity;

/**
 * ProductCharacteristic
 */
class ProductCharacteristic extends Entity
{

    /**
     * @var string
     */
    protected $product_id;

    /**
     * @var string
     */
    protected $characteristic_id;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var int
     */
    protected $sort_order;

    /**
     * @var string
     */
    protected $value;

    /**
     * Set product_id.
     *
     * @param string $productId
     *
     * @return ProductCharacteristic
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
     * Set characteristic_id.
     *
     * @param string $characteristicId
     *
     * @return ProductCharacteristic
     */
    public function setCharacteristicId($characteristicId)
    {
        $this->characteristic_id = $characteristicId;

        return $this;
    }

    /**
     * Get characteristic_id.
     *
     * @return string
     */
    public function getCharacteristicId()
    {
        return $this->characteristic_id;
    }

    /**
     * Set type.
     *
     * @param int $type
     *
     * @return ProductCharacteristic
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set sort_order.
     *
     * @param int $sortOrder
     *
     * @return ProductCharacteristic
     */
    public function setSortOrder($sortOrder)
    {
        $this->sort_order = $sortOrder;

        return $this;
    }

    /**
     * Get sort_order.
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sort_order;
    }

    /**
     * Set value.
     *
     * @param string $value
     *
     * @return ProductCharacteristic
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

}
