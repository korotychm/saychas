<?php

namespace ControlPanel\Model\Entity;

use Application\Model\Entity\Entity;
use ControlPanel\Model\Repository\RoleRepository;

/**
 * Description of RoleHierarchy
 *
 * @author alex
 */
class RoleHierarchy extends Entity
{
    public static RoleRepository $roleRepository;

    protected $id;
    
    protected $parent_role_id;
    
    protected $child_role_id;
    
    public function receiveRole()
    {
        return self::$roleRepository->find(['id' => $this->getParentRoleId()]);
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setParentRoleId($parentRoleId)
    {
        $this->parent_role_id = $parentRoleId;
        return $this;
    }
    
    public function getParentRoleId()
    {
        return $this->parent_role_id;
    }

    public function setChildRoleId($childRoleId)
    {
        $this->child_role_id = $childRoleId;
        return $this;
    }
    
    public function getChildRoleId()
    {
        return $this->child_role_id;
    }
}
