<?php
// src/Model/Entity/Category.php

namespace Application\Model\Entity;

class Categ
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $parent_id;
    
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $description;
    
    private $children;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    /**
     * @return string
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
     * @return string
     */
    public function getParentId()
    {
        return $this->parent_id;
    }
    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;
        return $this;
    }
    

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    public function getChildren() : Categ
    {
        return $this->children;
    }
    
    public function setChildren($children)
    {
        $this->children = $children;
        return $this;
    }
}
