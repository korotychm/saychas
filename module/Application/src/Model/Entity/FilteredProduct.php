<?php

// src/Model/Entity/FilteredProduct.php

namespace Application\Model\Entity;

//use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity
 */
class FilteredProduct
{

    /**
     * @var int
     *
     */
    private $id;

    /**
     * @var int
     */
    private $provider_id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $product_id;

    /**
     * @var string
     */
    private $product_title;

    /**
     * @var string
     *
     */
    private $rest;

    /**
     * @var int
     */
    private $price;

    /**
     * @var string
     */
    private $param_value_list;

    /**
     * @var string
     */
    private $param_variable_list;

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
     * @return string
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * @return string
     */
    public function getProductTitle()
    {
        return $this->product_title;
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

    public function getPrice()
    {
        return $this->price;
    }

    public function getParamValueList()
    {
        return $this->param_value_list;
    }

    public function getParamVariableList()
    {
        return $this->param_variable_list;
    }

}
