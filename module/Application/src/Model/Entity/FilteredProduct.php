<?php
// src/Model/FilteredProduct.php

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

//SELECT
//    `s`.`id` AS `id`,
//    `s`.`provider_id` AS `provider_id`,
//    `s`.`title` AS `title`,
//    `pr`.`id` AS `product_id`,
//    `sb`.`rest` AS `rest`
//FROM
//    `store` AS `s`
//INNER JOIN `provider` AS `p` ON `p`.`id` = `s`.`provider_id`
//INNER JOIN `product` AS `pr` ON `pr`.`provider_id` = `s`.`provider_id`
//LEFT JOIN `stock_balance` AS `sb` ON `sb`.`product_id` = `pr`.`id` AND `sb`.`store_id` = `s`.`id`
//WHERE `s`.`id` IN('000000005', '000000004');

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
    
}
