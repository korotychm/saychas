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

    public function receiveParentRoles()
    {
        $roleHierarchy = static::$roleHierarchyRepository->findAll([]);
        $currentRole = static::$roleHierarchyRepository->find(['id' => $this->getId()]);
        $elements = [];
        foreach($roleHierarchy as $element) {
            $terminal = $element->getTerminal();
            $elements[] = ['id' => $element->getId(), 'parent_id' => 1 == $terminal ? 0 : $element->getParentRoleId()];
        }        
        
//        echo '<pre>';
//        print_r($elements);
//        echo '</pre>';
//        
//        $tree = \Application\Helper\ArrayHelper::buildTree($elements, 0);
//        echo '<pre>';
//        print_r($tree);
//        echo '</pre>';
        
        //$parents = \Application\Helper\ArrayHelper::getParents(['id' => 6, 'parent_id' => 5], $elements);
        $parents = \Application\Helper\ArrayHelper::getParents(['id' => $currentRole->getId(), 'parent_id' => $currentRole->getParentRoleId()], $elements);
        echo '<pre>';
        print_r($parents);
        echo '</pre>';
        exit;
        
//        $results = [];
//        foreach($elements as $row) {
//            $role = static::$roleHierarchyRepository->find(['id' => $row['id']]);
//            $param = ['id' => (int)$row['id'], 'parent_role_id' => (int)$row['parent_id']];
//            $results[] = \Application\Helper\ArrayHelper::getParents($param, $elements, [], 'id', 'parent_role_id');
//        }
//        return $results;
    }
    
    public function getParentRoles()
    {
        echo 'banzaii';
        exit;
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
