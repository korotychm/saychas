<?php

// src/Model/Repository/ClientOrderRepository.php

namespace Application\Model\Repository;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Db\Sql\Select;
//use Laminas\Db\Sql\Join;
//use Laminas\Db\Sql\Sql;
//use Laminas\Db\Adapter\Driver\ResultInterface;
//use Laminas\Db\ResultSet\HydratingResultSet;
use Application\Model\Entity\ClientOrder;
use Application\Model\Repository\Repository;

class ClientOrderRepository extends Repository
{

    /**
     * @var string
     */
    protected $tableName = "client_order";

    /**
     * @var ClientOrder
     */
    protected ClientOrder $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param ClientOrder $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            ClientOrder $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;

        parent::__construct();
    }

//    public function persist($entity, $params, $hydrator = null)
//    {
//        if (null == $hydrator) {
//            $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();
//
//            $composite = new \Laminas\Hydrator\Filter\FilterComposite();
//            $composite->addFilter(
//                    'excludegettimestamp',
//                    new \Laminas\Hydrator\Filter\MethodMatchFilter('getTimestamp'),
//                    \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND
//            );
//            $composite->addFilter(
//                    'excludesettimestamp',
//                    new \Laminas\Hydrator\Filter\MethodMatchFilter('setTimestamp'),
//                    \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND
//            );
//            $hydrator->addFilter('excludes', $composite, \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND);
//        }
//
//        return parent::persist($entity, $params, $hydrator);
//    }
    
//    public function findAll($params)
//    {
//        $join = new Join();
//        $join->join(['rh' => 'role_hierarchy'], "{$this->tableName}.id = rh.id", ['parent_role_id'], Select::JOIN_LEFT);
//        $params['joins'] = $join;
//        return parent::findAll($params);
//    }

    /**
     * Adds given client_order into it's repository
     *
     * @param json
     */
    public function replace($content)
    {
        return ['result' => false, 'description' => 'not implemented', 'statusCode' => 405];
    }

}
