<?php

// src/Model/Entity/ProductImage.php

namespace Application\Model\Entity;

/**
 * ProductImage
 *
 * @ORM\Table(name="product_image")
 * @ORM\Entity
 */
class ProductImage
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $product_id;

    /**
     * @var string
     */
    private $ftp_url;

    /**
     * @var string
     */
    private $http_url;

    /**
     * @var int
     */
    private $sort_order = '0';

    /**
     * Get id.
     *
     * @return string
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
