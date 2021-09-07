<?php

// src/Model/Entity/HandbookRelatedProduct.php

namespace Application\Model\Entity;

use Application\Model\RepositoryInterface\BrandRepositoryInterface;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\ProductImageRepositoryInterface;
use Application\Model\RepositoryInterface\ProviderRepositoryInterface;
use Application\Model\RepositoryInterface\ProductCharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Application\Model\RepositoryInterface\MarkerRepositoryInterface;
use Application\Model\Repository\HandbookRelatedProductRepository;
use Application\Model\Traits\Searchable;

/**
 * HandbookRelatedProduct
 *
 * @ORM\Table(name="product")
 * @ORM\Entity
 */
class HandbookRelatedProduct extends Entity
{
    
    use Searchable;

    /**
     * @var HandbookRelatedProductRepository
     */
    public static HandbookRelatedProductRepository $repository;

    /**
     * @var BrandRepositoryInterface
     */
    public static BrandRepositoryInterface $brandRepository;

    /**
     * @var PriceRepositoryInterface
     */
    public static PriceRepositoryInterface $priceRepository;

    /**
     * @var ProductImageRepositoryInterface
     */
    public static ProductImageRepositoryInterface $productImageRepository;

    /**
     * @var ProviderRepositoryInterface
     */
    public static ProviderRepositoryInterface $providerRepository;

    /**
     * @var ProductCharacteristicRepositoryInterface
     */
    public static ProductCharacteristicRepositoryInterface $productCharacteristicRepository;

    /**
     * @var StockBalanceRepositoryInterface
     */
    public static StockBalanceRepositoryInterface $stockBalanceRepository;

    /**
     * @var MarkerRepositoryInterface
     */
    public static MarkerRepositoryInterface $markerRepository;

    /**
     * Get brand
     *
     * @return Brand
     * @throws \Exception
     */
    public function receiveBrandObject()
    {
        if (!( self::$brandRepository instanceof BrandRepositoryInterface )) {
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
    public function receivePriceObject()
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
    public function receiveProductImages()
    {
        if (!( self::$productImageRepository instanceof ProductImageRepositoryInterface )) {
            throw new \Exception('ProductImageRepositoryInterface expected; other type given');
        }
        return self::$productImageRepository->findAll(['where' => ['product_id' => $this->getId()]]);
    }

    /**
     * Return first product image from sorted row set;
     *
     * @return string
     * @throws \Exception
     */
    public function receiveFirstImageObject()
    {
        if (!( self::$productImageRepository instanceof ProductImageRepositoryInterface )) {
            throw new \Exception('ProductImageRepositoryInterface expected; other type given');
        }
        $images = self::$productImageRepository->findAll(['where' => ['product_id' => $this->getId()], 'order' => 'sort_order']);
        $image = $images->current();

        return $image;
    }

    /**
     * Return marker object;
     *
     * @return Marker
     * @throws \Exception
     */
    public function receiveMarkerObject()
    {
        if (!( self::$markerRepository instanceof MarkerRepositoryInterface )) {
            throw new \Exception('MarkerRepositoryInterface expected; other type given');
        }
        $marker = self::$markerRepository->find(['where' => ['id' => $this->getId()]]);

        return $marker->current();
    }

    /**
     *
     * @return Provider
     * @throws \Exception
     */
    public function getProvider()
    {
        if (!( self::$providerRepository instanceof ProviderRepositoryInterface )) {
            throw new \Exception('ProviderRepositoryInterface expected; other type given');
        }
        return self::$providerRepository->findFirstOrDefault(['id=?' => $this->getProviderId()]);
    }

    /**
     * Return summary value for rest
     *
     * @return int
     * @throws \Exception
     */
    public function receiveRest(array $storeIds = [])
    {
        if (!( self::$stockBalanceRepository instanceof StockBalanceRepositoryInterface )) {
            throw new \Exception('StockBalanceRepositoryInterface expected; other type given');
        }

        $select = new \Laminas\Db\Sql\Select();

        $select->from(self::$stockBalanceRepository->getTableName());
        $expression = new \Laminas\Db\Sql\Expression();
        $expression->setExpression('sum(rest)');

        $where = ['product_id' => $this->getId()];
        //if(count($storeIds) > 0) {
        $where = array_merge($where, ['store_id' => $storeIds]);
        //}
        $result = self::$stockBalanceRepository->findAll(['where' => $where, 'columns' => ['rest' => $expression], 'group' => ['product_id']/* , 'having' => ['product_id' => $this->getId()] */]);

        $current = $result->current();
        if (null != $current) {
            return $current->getRest();
        }
        return 0;
    }

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $provider_id;

    /**
     * @var string
     */
    protected $category_id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
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
     * Product price from joined price table
     *
     * @var int
     */
    protected $price;

    /**
     * Product old_price from joined price table
     *
     * @var int
     */
    protected $old_price;

    /**
     * Product discount from joined price table
     * @var int
     */
    protected $discount;

    /**
     * Vat
     *
     * @var int
     */
    protected $vat;

    /**
     * Get price from one-to-one joined price table
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set price. Needed when hydrating;
     *
     * @param type $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get old_price from one-to-one joined old_price table
     *
     * @return int
     */
    public function getOldPrice()
    {
        return $this->old_price;
    }

    /**
     * Set old_price. Needed when hydrating;
     *
     * @param int $oldPrice
     * @return $this
     */
    public function setOldPrice($oldPrice)
    {
        $this->old_price = $oldPrice;
        return $this;
    }

    /**
     * Get discount from one-to-one joined discount table
     *
     * @return int
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set discount. Needed when hydrating;
     *
     * @param int $discount
     * @return $this
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }

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
     * Set id.
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set providerId.
     *
     * @param string $providerId
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
     * @return string
     */
    public function getProviderId()
    {
        return $this->provider_id;
    }

    /**
     * Set categoryId.
     *
     * @param string $categoryId
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
     * @return string
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
        $this->vendor_code = $vendorCode;

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
        return $this->param_variable_list;
    }

    /**
     * Get brand_id
     *
     * @return string
     */
    public function getBrandId()
    {
        return $this->brand_id;
    }

    /**
     * Set brand_id
     *
     * @param string $brandId
     * @return $this
     */
    public function setBrandId($brandId)
    {
        $this->brand_id = $brandId;
        return $this;
    }

    /**
     * Get rest(balance)
     *
     * @return int
     */
    public function getRest()
    {
        return $this->rest;
    }

    /**
     * Set rest(balance)
     *
     * @param int $rest
     * @return $this
     */
    public function setRest($rest)
    {
        $this->rest = $rest;
        return $this;
    }

    /**
     * Get vat
     *
     * @return int
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * Set vat
     *
     * @param int $vat
     * @return $this
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
        return $this;
    }

}
