<?php
// src/Model/Product.php

namespace Application\Model\Entity;

//use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity
 */
class Product
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
     * @var int
     *
     * @ORM\Column(name="provider_id", type="integer", nullable=false)
     */
    private $provider_id;

    /**
     * @var int
     *
     * @ORM\Column(name="category_id", type="integer", nullable=false)
     */
    private $category_id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", length=65535, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_code", type="string", length=11, nullable=false)
     */
    private $vendor_code;

    /**
     * @var int
     */
    private $price;


    /**
     * @var int
     */
    private $rest;
    
    /**
     * @var string
     */
    private $url_http;

    /**
     * @var string
     */
    private $brand_title;

    private $store_id;
    
    private $store_title;
    
    public function getStoreId()
    {
        return $this->store_id;
    }
    
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
    private $paramValueList;

    /**
     * @var string
     *
     * @ORM\Column(name="param_variable_list", type="text", nullable=false)
     */
    private $paramVariableList;


    /**
     * Set paramValueList.
     *
     * @param string $paramValueList
     *
     * @return Product
     */
    public function setParamValueList($paramValueList)
    {
        $this->paramValueList = $paramValueList;

        return $this;
    }

    /**
     * Get paramValueList.
     *
     * @return string
     */
    public function getParamValueList()
    {
        return $this->paramValueList;
    }

    /**
     * Set paramVariableList.
     *
     * @param string $paramVariableList
     *
     * @return Product
     */
    public function setParamVariableList($paramVariableList)
    {
        $this->paramVariableList = $paramVariableList;

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
     * Get url_http.
     *
     * @return string
     */
    public function getUrlHttp()
    {
        return $this->url_http;
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
