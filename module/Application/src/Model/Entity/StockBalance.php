<?php
// src/Model/StockBalance.php

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
    private $productId;

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
    private $storeId;


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
     * Set productId.
     *
     * @param int $productId
     *
     * @return StockBalance
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
     * Set storeId.
     *
     * @param int $storeId
     *
     * @return StockBalance
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
}
