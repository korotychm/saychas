<?php

// src/Model/Entity/ProductImage.php

namespace Application\Model\Entity;

//use Doctrine\ORM\Mapping as ORM;

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
    private $product_id;

    /**
     * @var string
     *
     * @ORM\Column(name="ftp_url", type="text", length=65535, nullable=false)
     */
    private $ftp_url;

    /**
     * @var string
     *
     * @ORM\Column(name="http_url", type="text", length=65535, nullable=false)
     */
    private $http_url;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sort_order = '0';

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
     * @param string $productId
     *
     * @return ProductImage
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
     * Set ftp_url.
     *
     * @param string $ftpUrl
     *
     * @return ProductImage
     */
    public function setFtpUrl($ftpUrl)
    {
        $this->ftp_url = $ftpUrl;

        return $this;
    }

    /**
     * Get ftp_url.
     *
     * @return string
     */
    public function getFtpUrl()
    {
        return $this->ftp_url;
    }

    /**
     * Set http_url.
     *
     * @param string $httpUrl
     *
     * @return ProductImage
     */
    public function setHttpUrl($httpUrl)
    {
        $this->http_url = $httpUrl;

        return $this;
    }

    /**
     * Get http_url.
     *
     * @return string
     */
    public function getHttpUrl()
    {
        return $this->http_url;
    }

    /**
     * Set sort_order.
     *
     * @param int $sortOrder
     *
     * @return ProductImage
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

}
