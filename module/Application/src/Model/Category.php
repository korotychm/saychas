<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model;

class Category
{
//+-------------+---------+------+-----+---------+----------------+
//| id_group    | int(11) | NO   | PRI | NULL    | auto_increment |
//| group_name  | text    | NO   |     | NULL    |                |
//| parent      | int(11) | NO   |     | NULL    |                |
//| comment     | text    | NO   |     | NULL    |                |
//| id_1C_group | int(11) | NO   | UNI | NULL    |                |
//+
    
    /**
     * @var string
     */
    private $group_name;

    /**
     * @var int
     */
    private $parent;
    
    /**
     * @var int
     */
    private $id_1C_group;

    /**
     * @var string
     */
    private $comment;

    /**
     * @param string      $groupName
     * @param int         $id1cGroup
     * @param int         $parent
     * @param string|null $icon
     * @param string|null $comment
     */
    public function __construct($groupName, $id1cGroup, $parent, $icon = null, $comment = null)
    {
        $this->group_name = $groupName;
        $this->parent = $parent;
        $this->comment = $comment;
        $this->id_1C_group = $id1cGroup;
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->group_name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id_1C_group;
    }
    /**
     * @return int
     */
    public function getParent()
    {
        return $this->parent;
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
