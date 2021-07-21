<?php

// ControlPanel/src/Model/Entity/RolePermission.php

namespace ControlPanel\Model\Entity;

use Application\Model\Entity\Entity;

/**
 * Description of RolePermission
 *
 * @author alex
 */
class RolePermission extends Entity
{

    public static $repository;

    /** @var int */
    protected $role_id;

    /** @var int */
//    protected $parent_role_id;

    /** @var string */
    protected $role_name;

    /** @var string */
    protected $permission_name;

    /**
     * Set id
     *
     * @param int $role_id
     * @return $this
     */
    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;
        return $this;
    }

    /**
     * Get role_id
     *
     * @return int
     */
    public function getRoleId()
    {
        return $this->role_id;
    }

    /**
     * Set role_name
     *
     * @param string $role_name
     * @return $this
     */
    public function setRoleName($roleName)
    {
        $this->role_name = $roleName;
        return $this;
    }

    /**
     * Get role_name
     *
     * @return string
     */
    public function getRoleName()
    {
        return $this->role_name;
    }

    /**
     * Set permission_name
     *
     * @param string $permissionName
     * @return $this
     */
    public function setPermissionName($permissionName)
    {
        $this->permission_name = $permissionName;
        return $this;
    }

    /**
     * Get permission_name
     *
     * @return string
     */
    public function getPermissionName()
    {
        return $this->permission_name;
    }

}
