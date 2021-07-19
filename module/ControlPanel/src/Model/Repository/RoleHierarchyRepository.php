<?php

// src/Model/Repository/RoleHierarchyRepository.php

namespace ControlPanel\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use ControlPanel\Model\Entity\RoleHierarchy;
use Application\Model\Repository\Repository;

class RoleHierarchyRepository extends Repository
{

    /**
     * @var string
     */
    protected $tableName = "role_hierarchy";

    /**
     * @var RoleHierarchy
     */
    protected RoleHierarchy $prototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param RoleHierarchy $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            RoleHierarchy $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
        
        parent::__construct();
    }
    
    /**
     * Adds given role_hierarchy into it's repository
     *
     * @param json
     */
    public function replace($content)
    {
        return ['result' => false, 'description' => 'not implemented', 'statusCode' => 405];
    }

}
