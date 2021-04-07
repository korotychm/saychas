<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * StockBalance
 *
 * @ORM\Table(name="stock_balance")
 * @ORM\Entity
 */
class StockBalance
{
    /**
     * @var string
     *
     * @ORM\Column(name="product_id", type="string", length=12, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $productId;

    /**
     * @var string
     *
     * @ORM\Column(name="store_id", type="string", length=9, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $storeId;

    /**
     * @var int
     *
     * @ORM\Column(name="rest", type="integer", nullable=false)
     */
    private $rest;


    /**
     * Set productId.
     *
     * @param string $productId
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
     * @return string
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set storeId.
     *
     * @param string $storeId
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
     * @return string
     */
    public function getStoreId()
    {
        return $this->storeId;
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
}
