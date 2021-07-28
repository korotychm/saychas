<?php

// ControlPanel/src/Model/Entity/Role.php

namespace ControlPanel\Model\Entity;

use Application\Model\Entity\Entity;
use ControlPanel\Model\Repository\RoleHierarchyRepository;
use ControlPanel\Model\Repository\RoleRepository;
use Application\Model\Traits\Searchable;

/**
 * Description of Role
 *
 * @author alex
 */
class Role extends Entity
{

    use Searchable;
    
    public static RoleHierarchyRepository $roleHierarchyRepository;
    
    public static RoleRepository $repository;

    /** @var int */
    protected $id;

    /** @var int */
//    protected $parent_role_id;

    /** @var string */
    protected $name;
    
    /** @var string */
    protected $role;

    /** @var string */
    protected $description;

    /** @var string */
    protected $date_created;
    
    protected $parentRoles = [];
    
    protected $childRoles = [];

    /**
     * Set id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
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
     * Set parent_role_id
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
     * Get parent_role_id
     *
     * @return int
     */
    public function getParentRoleId()
    {
        return $this->parent_role_id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set date_created
     *
     * @param string $dateCreated
     * @return $this
     */
    public function setDateCreated($dateCreated)
    {
        $this->date_created = $dateCreated;
        return $this;
    }

    /**
     * Get date_created
     *
     * @return string
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }
    
    public function getParentRoles()
    {
        
        $hierarchy = self::$roleHierarchyRepository->findAll([])->toArray();
        $parents = \Application\Helper\ArrayHelper::getParents(
                        ['child_role_id' => $this->getId(), 'parent_role_id' => $this->getParentRoleId()],
                        $hierarchy/* $roles */,
                        [],
                        'child_role_id',
                        'parent_role_id'
        );
        foreach ($parents as $parentId) {
            $parentRole = self::$repository->find(['id' => $parentId]);
            $this->parentRoles[] = $parentRole;
        }
        
        return $this->parentRoles;
    }
    
    public function getChildRoles()
    {
        return $this->childRoles;
    }
    

}
