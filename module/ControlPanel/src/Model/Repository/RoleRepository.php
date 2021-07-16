<?php

// src/Model/Repository/RoleRepository.php

namespace ControlPanel\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use ControlPanel\Model\Entity\Role;
use Application\Model\Repository\Repository;

class RoleRepository extends Repository
{

    /**
     * @var string
     */
    protected $tableName = "role";

    /**
     * @var Role
     */
    protected Role $prototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Role $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            Role $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
        
        parent::__construct();
    }
    
    public function getRoles()
    {
        return 'roles';
    }

    /**
     * Adds given role into it's repository
     *
     * @param json
     */
    public function replace($content)
    {
        return ['result' => false, 'description' => 'not implemented', 'statusCode' => 405];
    }

}
