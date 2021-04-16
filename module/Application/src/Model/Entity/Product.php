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
     * @var int
     */
    protected $price;


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
     * @var string (json)
     */
    protected $param_variable_list;
    

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
     * @var string
     *
     * @ORM\Column(name="param_value_list", type="text", length=65535, nullable=false)
     */
//    private $paramValueList;

    /**
     * @var string
     *
     * @ORM\Column(name="param_variable_list", type="text", nullable=false)
     */
    //private $paramVariableList;


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
}
