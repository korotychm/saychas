<?php

// src/Model/Entity/Characteristic.php

namespace Application\Model\Entity;

use Application\Model\Repository\CharacteristicRepository;
use Application\Model\Traits\Searchable;

/**
 * Characteristic
 */
class Characteristic extends Entity
{
    
    use Searchable;

    /**
     * @var CharacteristicRepository
     */
    public static CharacteristicRepository $repository;

    /**
     * characteristic_id
     * @var string, length=9
     */
    protected $id;

    /**
     * characteristic_title
     * @var string|null
     */
    protected $title;

    /**
     * characteristic_type
     * @var int
     */
    protected $type;

    /**
     * @var string, length=9
     */
    protected $category_id;

    /**
     * @var int
     */
    protected $filter;

    /**
     * @var int
     */
    protected $group;

    /**
     * sort_order
     * @var int
     */
    private $sort_order;

    /**
     * unit
     * @var string
     */
    protected $unit;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var bool
     */
    protected $is_main;

    /**
     * @var bool
     */
    protected $is_mandatory;

    /**
     * @var bool
     */
    protected $is_list;

    /**
     * val
     * @var string
     */
    protected $val;

    /**
     * val_id
     * @var string
     */
    protected $val_id;

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
     * Get characteristic_value  value.
     *
     * @return string
     */
    public function getVal()
    {
        return $this->val;
    }

    /**
     * Get characteristic_value  id.
     *
     * @return string
     */
    public function getValId()
    {
        return $this->val_id;
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
     * Set category_id.
     *
     * @param string, length=9
     *
     * @return Characteristic
     */
    public function setCategoryId($categoryId)
    {
        $this->category_id = $categoryId;

        return $this;
    }

    /**
     * Get category_id.
     *
     * @return string
     */
    public function getCategoryId()
    {
        return $this->category_id;
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
     * Set sort_order.
     *
     * @param int $sortOrder
     *
     * @return Characteristic
     */
    public function setSortOrder($sortOrder)
    {
        $this->sort_order = $sortOrder;
        return $this;
    }

    /**
     * Get sort_order.
     *
     * @return string
     */
    public function getSortOrder()
    {
        return $this->sort_order;
    }

    /**
     * Set unit
     *
     * @param string $unit
     * @return $this
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * Get unit.
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set description.
     *
     * @param string $description
     * @return $this
     */
    public function setDesctiption($description)
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
     * Set is_main
     *
     * @param bool $isMain
     * @return $this
     */
    public function setIsMain($isMain)
    {
        $this->is_main = $isMain;
        return $this;
    }

    /**
     * Get is_main
     *
     * @return bool
     */
    public function getIsMain()
    {
        return $this->is_main;
    }

    /**
     * Set is_mandatory
     *
     * @param bool $isMandatory
     * @return $this
     */
    public function setIsMandatory($isMandatory)
    {
        $this->is_mandatory = $isMandatory;
        return $this;
    }

    /**
     * Get is_mandatory
     *
     * @return bool
     */
    public function getIsMandatory()
    {
        return $this->is_mandatory;
    }

    /**
     * Set is_list
     *
     * @param bool $isList
     * @return $this
     */
    public function setIsList($isList)
    {
        $this->is_list = $isList;
        return $this;
    }

    /**
     * Get is_list
     *
     * @return bool
     */
    public function getIsList()
    {
        return $this->is_list;
    }

}
