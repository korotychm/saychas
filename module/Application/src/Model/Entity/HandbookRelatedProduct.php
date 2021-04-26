<?php

// src/Model/Entity/HandbookRelatedProduct.php

namespace Application\Model\Entity;

//use Doctrine\ORM\Mapping as ORM;

use Application\Model\Repository\BrandRepository;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\ProductImageRepositoryInterface;

/**
 * HandbookRelatedProduct
 *
 * @ORM\Table(name="product")
 * @ORM\Entity
 */
class HandbookRelatedProduct extends Entity
{

    /**
     * @var BrandRepositoryInterface
     */
    public static BrandRepository $brandRepository;

    /**
     * @var PriceRepositoryInterface
     */
    public static PriceRepositoryInterface $priceRepository;

    /**
     * @var ProductImageRepositoryInterface
     */
    public static ProductImageRepositoryInterface $productImageRepository;

    /**
     * Get brand
     *
     * @return Brand
     * @throws \Exception
     */
    public function getBrand()
    {
        if (!( self::$brandRepository instanceof BrandRepository )) {
            throw new \Exception('BrandRepositoryInterface expected; other type given');
        }
        return self::$brandRepository->findFirstOrDefault(['id' => $this->getBrandId()]);
    }

    /**
     * Get price
     *
     * @return Price
     * @throws \Exception
     */
    public function getPrice()
    {
        if (!( self::$priceRepository instanceof PriceRepositoryInterface )) {
            throw new \Exception('PriceRepositoryInterface expected; other type given');
        }
        //'pr.id = pri.product_id'
        return self::$priceRepository->findFirstOrDefault(['product_id=?' => $this->getId()]);
    }

    /**
     * Get productImages
     *
     * @return ProductImage[]
     * @throws \Exception
     */
    public function getProductImages()
    {
        if (!( self::$productImageRepository instanceof ProductImageRepositoryInterface )) {
            throw new \Exception('ProductImageRepositoryInterface expected; other type given');
        }
        return self::$productImageRepository->findAll(['where' => ['product_id' => $this->getId()] ]);
        //'pr.id = img.product_id'
//        return self::$productImageRepository->findFirstOrDefault(['product_id' => $this->getId()]);
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(name="provider_id", type="integer", nullable=false)
     */
    protected $provider_id;

    /**
     * @var int
     *
     * @ORM\Column(name="category_id", type="integer", nullable=false)
     */
    protected $category_id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", length=65535, nullable=false)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_code", type="string", length=11, nullable=false)
     */
    protected $vendor_code;

    /**
     * @var string
     */
    protected $param_value_list;

    /**
     * @var string (json)
     */
    protected $param_variable_list;

    /**
     * @var string
     */
    protected $brand_id;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set providerId.
     *
     * @param int $providerId
     * @return Product
     */
    public function setProviderId($providerId)
    {
        $this->provider_id = $providerId;

        return $this;
    }

    /**
     * Get providerId.
     *
     * @return int
     */
    public function getProviderId()
    {
        return $this->provider_id;
    }

    /**
     * Set categoryId.
     *
     * @param int $categoryId
     * @return Product
     */
    public function setCategoryId($categoryId)
    {
        $this->category_id = $categoryId;

        return $this;
    }

    /**
     * Get categoryId.
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * Set title.
     *
     * @param string $title
     * @return Product
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set vendorCode.
     *
     * @param string $vendorCode
     *
     * @return Product
     */
    public function setVendorCode($vendorCode)
    {
        $this->vendor_code = $vendor_code;

        return $this;
    }

    /**
     * Get vendorCode.
     *
     * @return string
     */
    public function getVendorCode()
    {
        return $this->vendor_code;
    }

    /**
     * Set paramValueList.
     *
     * @param string $param_value_list
     *
     * @return Product
     */
    public function setParamValueList($paramValueList)
    {
        $this->param_value_list = $paramValueList;

        return $this;
    }

    /**
     * Get param_value_list.
     *
     * @return string
     */
    public function getParamValueList()
    {
        return $this->param_value_list;
    }

    /**
     * Set param_variable_list.
     *
     * @param string $paramVariableList
     *
     * @return Product
     */
    public function setParamVariableList($paramVariableList)
    {
        $this->param_variable_list = $paramVariableList;

        return $this;
    }

    /**
     * Get paramVariableList.
     *
     * @return string
     */
    public function getParamVariableList()
    {
        return $this->paramVariableList;
    }

    public function getBrandId()
    {
        return $this->brand_id;
    }

    public function setBrandId($brandId)
    {
        $this->brand_id = $brandId;
        return $this;
    }

}
