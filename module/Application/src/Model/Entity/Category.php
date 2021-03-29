<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model\Entity;

class Category
{
    /**
     * @var string
     */
    //private $group_name;
    private $title;

    /**
     * @var string
     */
    //private $parent;
    private $parent_id;
    
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    //private $comment;
    private $description;

    /**
     * @param string      $title
     * @param string      $id
     * @param string      $parent_id
     * @param string|null $icon
     * @param string|null $description
     */
    public function __construct($title, $id, $parentId, $icon = null, $description = null)
    {
        $this->title = $title;
        $this->parent_id = $parentId;
        $this->description = $description;
        $this->id = $id;
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @return int
     */
    public function getParent()
    {
        return $this->parent_id;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->description;
    }
}
