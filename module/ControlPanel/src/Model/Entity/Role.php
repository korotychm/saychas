<?php

// ControlPanel/src/Model/Entity/Role.php

namespace ControlPanel\Model\Entity;

use Application\Model\Entity\Entity;
use ControlPanel\Model\Repository\RoleHierarchyRepository;

/**
 * Description of Role
 *
 * @author alex
 */
class Role extends Entity
{
    
    public static RoleHierarchyRepository $roleHierarchyRepository;

    /** @var int */
    protected $id;
    
    /** @var int */
//    protected $parent_role_id;
    
    /** @var string */
    protected $name;
    
    /** @var string */
    protected $description;
    
    /** @var string */
    protected $date_created;

    public function receiveParantRoles()
    {
        return static::$roleHierarchyRepository->findAll(['where' => ['parent_role_id' => $this->getId()] ]);
    }
    
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
//    public function setParentRoleId($parentRoleId)
//    {
//        $this->parent_role_id = $parentRoleId;
//        return $this;
//    }

    /**
     * Get parent_role_id
     * 
     * @return int
     */
//    public function getParentRoleId()
//    {
//        return $this->parent_role_id;
//    }

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

}
