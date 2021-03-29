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
     * @ORM\Column(name="url_ftp", type="text", length=65535, nullable=false)
     */
    private $urlFtp;

    /**
     * @var string
     *
     * @ORM\Column(name="url_http", type="text", length=65535, nullable=false)
     */
    private $urlHttp;

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
     * Set urlFtp.
     *
     * @param string $urlFtp
     *
     * @return ProductImage
     */
    public function setUrlFtp($urlFtp)
    {
        $this->urlFtp = $urlFtp;

        return $this;
    }

    /**
     * Get urlFtp.
     *
     * @return string
     */
    public function getFtpUrl()
    {
        return $this->urlFtp;
    }

    /**
     * Set urlHttp.
     *
     * @param string $urlHttp
     *
     * @return ProductImage
     */
    public function setHttpUrl($httpUrl)
    {
        $this->httpUrl = $ttp;

        return $this;
    }

    /**
     * Get urlHttp.
     *
     * @return string
     */
    public function getUrlHttp()
    {
        return $this->urlHttp;
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
