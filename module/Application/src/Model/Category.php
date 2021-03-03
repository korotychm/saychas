<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model;

class Category
{
    /**
     * @var int
     */
    private $idGroup;

    /**
     * @var string
     */
    private $groupName;

    /**
     * @var int
     */
    private $parent;
    
    /**
     * @var int
     */
    private $id1cGroup;

    /**
     * @var string
     */
    private $comment;

    /**
     * @param int         $idGroup
     * @param string      $groupName
     * @param int         $id1cGroup
     * @param int         $parent
     * @param string|null $icon
     * @param string|null $comment
     */
    public function __construct($idGroup, $groupName, $id1cGroup, $parent, $icon = null, $comment = null)
    {
        $this->groupName = $groupName;
        $this->idGroup = $idGroup;
        $this->parent = $parent;
        $this->comment = $comment;
        $this->id1cGroup = $id1cGroup;
        $this->icon = $icon;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->idGroup;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->groupName;
    }

    /**
     * @return int
     */
    public function getId1cGroup()
    {
        return $this->id1cGroup;
    }
    /**
     * @return int
     */
    public function getParent()
    {
        return $this->id1cGroup;
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
        return $this->comment;
    }
}
