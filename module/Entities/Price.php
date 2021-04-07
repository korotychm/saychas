<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Price
 *
 * @ORM\Table(name="price")
 * @ORM\Entity
 */
class Price
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
     * @ORM\Column(name="provider_id", type="string", length=6, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $providerId;

    /**
     * @var int
     *
     * @ORM\Column(name="reserve", type="integer", nullable=false)
     */
    private $reserve;

    /**
     * @var string
     *
     * @ORM\Column(name="store_id", type="string", length=9, nullable=false)
     */
    private $storeId;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="text", length=255, nullable=false)
     */
    private $unit;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer", nullable=false)
     */
    private $price;


    /**
     * Set productId.
     *
     * @param string $productId
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
     * @return string
     */
    public function getProductId()
    {
        return $this->productId;
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
     * @param string $storeId
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
     * @return string
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
}
