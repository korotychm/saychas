<?php
// src/Model/Entity/Characteristic.php

namespace Application\Model\Entity;

//use Doctrine\ORM\Mapping as ORM;

/**
 * Characteristic
 *
 * @ORM\Table(name="characteristic")
 * @ORM\Entity
 */
class Characteristic
{
    /**
     * characteristic_id
     * @var string, length=9
     */
    private $id;

    /**
     * characteristic_title
     * @var string|null
     */
    private $title;

    /**
     * * characteristic_type
     * @var int
     */
    private $type;

    /**
     * @var string, length=9
     */
    private $categoryId;
    
    /**
     * @var int
     */
    private $filter;
    
    /**
     * @var int
     */
    private $group;
    
    /**
     * sortOrder
     * @var int
     */
    private $sortOrder;

    public function __construct($object) {
        $this->id = $object->id;
        $this->title = $object->title;
        $this->type = $object->type;
        $this->filter = $object->filter;
        $this->group = $object->group;
        $this->categoryId = $object->category_id;
        $this->sortOrder = $object->sort_order;
    }
    /**
     * Set characteristic id.
     *
     * @param string $id
     *
     * @return Characteristic
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * Get characteristic id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Characteristic
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
     * Set type.
     *
     * @param int $type
     *
     * @return Characteristic
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Set categoryId.
     *
     * @param string, length=9
     *
     * @return Characteristic
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }
    
    /**
     * Get type.
     *
     * @return string
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set filter.
     *
     * @param int $filter
     *
     * @return Characteristic
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Get filter.
     *
     * @return string
     */
    public function getFilter()
    {
        return $this->filter;
    }
    

    /**
     * Set group.
     *
     * @param int $group
     *
     * @return Characteristic
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }    
    
    /**
     * Get group.
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }
    
    /**
     * Set sortOrder.
     *
     * @param int $sortOrder
     *
     * @return Characteristic
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }    
    
    /**
     * Get sort_order.
     *
     * @return string
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
    
}
