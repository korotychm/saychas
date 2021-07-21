<?php

// src/Model/Repository/RoleRepository.php

namespace ControlPanel\Model\Repository;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Join;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use ControlPanel\Model\Entity\Role;
use ControlPanel\Model\Entity\RolePermission;
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

    public function getPermissions($roleId)
    {
        $select = new Select();
        $select->from('role_permissions');
        $select->columns(['role_id', 'role_name', 'permission_name']);
        $select->where(['role_id' => $roleId]);

        $sql = new Sql($this->db);
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            return [];
        }

        $resultSet = new HydratingResultSet(
                $this->hydrator,
                new RolePermission()
        );
        $resultSet->initialize($result);
        //$arr = $resultSet->toArray();
        return $resultSet; //$arr;
    }

    public function findAll($params)
    {
        $join = new Join();
        $join->join(['rh' => 'role_hierarchy'], "{$this->tableName}.id = rh.id", ['parent_role_id'], Select::JOIN_LEFT);
        $params['joins'] = $join;
        return parent::findAll($params);
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
