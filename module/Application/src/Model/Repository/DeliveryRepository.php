<?php

// src/Model/Repository/DeliveryRepository.php

namespace Application\Model\Repository;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Db\Sql\Select;
//use Laminas\Db\Sql\Join;
//use Laminas\Db\Sql\Sql;
//use Laminas\Db\Adapter\Driver\ResultInterface;
//use Laminas\Db\ResultSet\HydratingResultSet;
use Application\Model\Entity\Delivery;
use Application\Model\Repository\Repository;

class DeliveryRepository extends Repository
{

    /**
     * @var string
     */
    protected $tableName = "delivery";

    /**
     * @var Delivery
     */
    protected Delivery $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Delivery $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            Delivery $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;

        parent::__construct();
    }

//    public function findAll($params)
//    {
//        $join = new Join();
//        $join->join(['rh' => 'role_hierarchy'], "{$this->tableName}.id = rh.id", ['parent_role_id'], Select::JOIN_LEFT);
//        $params['joins'] = $join;
//        return parent::findAll($params);
//    }

    /**
     * Adds given delivery into it's repository
     *
     * @param json
     */
    public function replace($content)
    {
        return ['result' => false, 'description' => 'not implemented', 'statusCode' => 405];
    }

}
