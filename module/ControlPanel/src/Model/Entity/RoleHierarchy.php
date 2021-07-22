<?php

// ControlPanel/src/Model/Repository/RoleHierarchy.php

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

    /** @var RoleRepository */
    public static RoleRepository $roleRepository;

    /** @var int */
    protected $id;

    /** @var int */
    protected $parent_role_id;

    /** @var int */
    protected $child_role_id;

    /** @var int */
    protected $terminal;

    /**
     * Find role in roleRepository
     *
     * @return Role
     */
//    public function receiveRole()
//    {
//        return static::$roleRepository->find(['id' => $this->getParentRoleId()]);
//    }

    /**
     * Set id
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set parent role id
     *
     * @param int $parentRoleId
     * @return $this
     */
    public function setParentRoleId($parentRoleId)
    {
        $this->parent_role_id = $parentRoleId;
        return $this;
    }

    /**
     * Get parent role id
     *
     * @return int
     */
    public function getParentRoleId()
    {
        return $this->parent_role_id;
    }

    /**
     * Set child role id
     *
     * @param int $childRoleId
     * @return $this
     */
    public function setChildRoleId($childRoleId)
    {
        $this->child_role_id = $childRoleId;
        return $this;
    }

    /**
     * Get child role id
     *
     * @return int
     */
    public function getChildRoleId()
    {
        return $this->child_role_id;
    }

    /**
     * Set terminal
     *
     * @param int $terminal attribute
     * @return $this
     */
    public function setTerminal($terminal)
    {
        $this->terminal = $terminal;
        return $this;
    }

    /**
     * Get terminal attribute
     *
     * @return int
     */
    public function getTerminal()
    {
        return $this->terminal;
    }

}
