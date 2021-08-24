<?php

// src/Model/Entity/Product.php

namespace Application\Model\Entity;

//use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity
 */
class Product extends Entity
{

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
     * @var int
     */
    protected $price;

    
    /**
     * @var int
     */
    protected $old_price;

    
    /**
     * @var int
     */
    protected $rest;

    /**
     * @var string
     */
    protected $http_url;

    /**
     * @var string
     */
    protected $brand_title;
     /**
     * @var string
     */
    protected $brand_id;

    /**
     * @var string, length=9
     */
    protected $store_id;

    /**
     * @var string
     */
    protected $store_title;

    /**
     * @var string
     */
    protected $product_title;

    /**
     * @var string
     */
    protected $param_value_list;
    
    /**
     * @var string
     */
    protected $color;

    /**
     * @var string
     */
    protected $size;

    /**
     * @var string (json)
     */
    protected $param_variable_list;
    
    protected $tax;
    
    public function getTax()
    {
        return $this->tax;
    }
    
    public function setTax($tax)
    {
        $this->tax = $tax;
        return $this;
    }

    /**
     * Set color.
     * 
     * @param string $color
     * @return $this
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }
    
    /**
     * Get color.
     * 
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }
    
    /**
     * Set size.
     * 
     * @param string $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }
    
    /**
     * Get size.
     * 
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getParamValueList2()
    {
        return $this->param_value_list;
    }

    public function getParamVariableList2()
    {
        return $this->param_variable_list;
    }

    /**
     * Get product_title.
     * 
     * @return string
     */
    public function getProductTitle()
    {
        return $this->product_title;
    }

    /**
     * @return string, length=9
     */
    public function getStoreId()
    {
        return $this->store_id;
    }

    /**
     * @return string
     */
    public function getStoreTitle()
    {
        return $this->store_title;
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
     * Set providerId.
     *
     * @param int $providerId
     *
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
     *
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
     *
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
        return $this->param_variable_list;
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

    /**
     * Set old_price
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
     * Get old_price.
     *
     * @return int
     */
    public function getOldPrice()
    {
        return $this->old_price;
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
     * Get http_url.
     *
     * @return string
     */
    public function getHttpUrl()
    {
        return $this->http_url;
    }

    /**
     * Get brand_title.
     *
     * @return string
     */
    public function getBrandTitle()
    {
        return $this->brand_title;
    }
    /**
     * Get brand_title.
     *
     * @return string
     */
    public function getBrandId()
    {
        return $this->brand_id;
    }

}
