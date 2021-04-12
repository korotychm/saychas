<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ProductImage
 *
 * @ORM\Table(name="product_image")
 * @ORM\Entity
 */
class ProductImage
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
     * @var string
     *
     * @ORM\Column(name="product_id", type="string", length=100, nullable=false)
     */
    private $productId;

    /**
     * @var string
     *
     * @ORM\Column(name="ftp_url", type="text", length=65535, nullable=false)
     */
    private $ftpUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="http_url", type="text", length=65535, nullable=false)
     */
    private $httpUrl;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder = '0';


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
     * @param string $productId
     *
     * @return ProductImage
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
     * Set ftpUrl.
     *
     * @param string $ftpUrl
     *
     * @return ProductImage
     */
    public function setFtpUrl($ftpUrl)
    {
        $this->ftpUrl = $ftpUrl;

        return $this;
    }

    /**
     * Get ftpUrl.
     *
     * @return string
     */
    public function getFtpUrl()
    {
        return $this->ftpUrl;
    }

    /**
     * Set httpUrl.
     *
     * @param string $httpUrl
     *
     * @return ProductImage
     */
    public function setHttpUrl($httpUrl)
    {
        $this->httpUrl = $httpUrl;

        return $this;
    }

    /**
     * Get httpUrl.
     *
     * @return string
     */
    public function getHttpUrl()
    {
        return $this->httpUrl;
    }

    /**
     * Set sortOrder.
     *
     * @param int $sortOrder
     *
     * @return ProductImage
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder.
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}
