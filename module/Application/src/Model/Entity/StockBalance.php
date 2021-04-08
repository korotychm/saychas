<?php
// src/Model/Entity/StockBalance.php

namespace Application\Model\Entity;

//use Doctrine\ORM\Mapping as ORM;

/**
 * StockBalance
 *
 * @ORM\Table(name="stock_balance", uniqueConstraints={@ORM\UniqueConstraint(name="product_id", columns={"product_id", "store_id"})})
 * @ORM\Entity
 */
class StockBalance
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="product_id", type="integer", nullable=false)
     */
    private $product_id;

    /**
     * @var int
     *
     * @ORM\Column(name="rest", type="integer", nullable=false)
     */
    private $rest;

    /**
     * @var int
     *
     * @ORM\Column(name="store_id", type="integer", nullable=false)
     */
    private $store_id;


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
     * Set product_id.
     *
     * @param int $product_id
     *
     * @return StockBalance
     */
    public function setProductId($product_id)
    {
        $this->product_id = $product_id;

        return $this;
    }

    /**
     * Get product_id.
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set rest.
     *
     * @param int $rest
     *
     * @return StockBalance
     */
    public function setRest($rest)
    {
        $this->rest = $rest;

        return $this;
    }

    /**
     * Get rest.
     *
     * @return int
     */
    public function getRest()
    {
        return $this->rest;
    }

    /**
     * Set store_id.
     *
     * @param int $store_id
     *
     * @return StockBalance
     */
    public function setStoreId($store_id)
    {
        $this->store_id = $store_id;

        return $this;
    }

    /**
     * Get store_id.
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->store_id;
    }
}
