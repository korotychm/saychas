<?php

// src/Model/Entity/Basket.php

namespace Application\Model\Entity;

/**
 * Basket
 *
 * @ORM\Table(name="basket")
 * @ORM\Entity
 */
class Basket extends Entity
{
    
//CREATE TABLE IF NOT EXISTS `basket` (
//        `user_id` INT(11) NOT NULL,
//        `product_id` VARCHAR(12) NOT NULL,
//        `total` INT(11) NOT NULL DEFAULT 0,
//        `order_id` INT(11) NOT NULL,
//        `price` INT(11) NOT NULL DEFAULT 0,
//        `discount` INT(11) NOT NULL DEFAULT 0,
//        `discount_description` TEXT,
//        `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    /**
     * @var int
     */
    protected $user_id = 0;

    /**
     * @var string
     */
    protected $product_id;

    /**
     * @var int
     */
    protected $total = 0;

    /**
     * @var int
     */
    protected $order_id = 0;
    
    /**
     * @var int
     */
    protected $price = 0;
    
    /**
     * @var int
     */
    protected $discount = 0;

    /**
     * @var string
     */
    protected $discount_description;

    /**
     * @var timestamp
     */
    protected $timestamp;

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

    /**
     * Set product_id.
     *
     * @param string $productId
     *
     * @return Basket
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
     * Set discount description.
     *
     * @param string $discountDescription
     * @return Basket
     */
    public function setDiscountDescription($discountDescription)
    {
        $this->discount_description = $discountDescription;

        return $this;
    }

    /**
     * Get discount description.
     *
     * @return string
     */
    public function getDiscountDescription()
    {
        return $this->discount_description;
    }

    /**
     * Set discount.
     *
     * @param int $discount
     *
     * @return Basket
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount.
     *
     * @return int
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set total.
     *
     * @param int $total
     *
     * @return Basket
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }
    /**
     * Set order_id.
     *
     * @param int $orderId
     * @return Basket
     */

    public function setOrderId($orderId)
    {
        $this->order_id = $orderId;

        return $this;
    }

    /**
     * Get order_id.
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->order_id;
    }
    
    /**
     * Set price.
     *
     * @param int $price
     * @return Basket
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
    
}
