<?php

namespace ControlPanel\Model\Identity;

use Laminas\Permissions\Rbac\AbstractRole;
use Laminas\Permissions\Rbac\RoleInterface;
use Laminas\Permissions\Rbac\Role;

class Identity extends AbstractRole
{
    /**
     * @param string $username
     * @param string $role
     * @param array $permissions
     * @param array $childRoles
     */
    public function __construct(
        string $username,
        array $permissions = [],
        array $childRoles = []
    ) {
        // $name is defined in AbstractRole
        $this->name = $username;

        foreach ($this->permissions as $permission) {
            $this->addPermission($permission);
        }

        $childRoles = array_merge(['guest'], $childRoles);
        foreach ($this->childRoles as $childRole) {
            $this->addChild($childRole);
        }
    }
}