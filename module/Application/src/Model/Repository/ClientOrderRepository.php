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
use Laminas\Json\Json;
//use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
//use Laminas\Db\Adapter\Exception\InvalidQueryException;
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

    public function persist($entity, $params, $hydrator = null)
    {
        if (null == $hydrator) {
            $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();

            $composite = new \Laminas\Hydrator\Filter\FilterComposite();
            $composite->addFilter(
                    'excludegettimestamp',
                    new \Laminas\Hydrator\Filter\MethodMatchFilter('getTimestamp'),
                    \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND
            );
            $composite->addFilter(
                    'excludesettimestamp',
                    new \Laminas\Hydrator\Filter\MethodMatchFilter('setTimestamp'),
                    \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND
            );
            $hydrator->addFilter('excludes', $composite, \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND);
        }

        return parent::persist($entity, $params, $hydrator);
    }
    
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
    /**
     * receive-client-order-statuses
        {
            "orderId": "123",
            "order_status": "0",
            "deliveries": 
                    [
                        {
                            "delivery_id": "123123",
                            "delivery_status": 0
                        }, {
                            "delivery_id": "123124",
                            "delivery_status": 1
                        }
                    ],
            "applications": 
                    [
                        {
                            "application_id": "123123",
                            "application_status": 0
                        }, {
                            "application_id": "123124",
                            "application_status": 1
                        }
                    ]
        }
        { "orderId": "123", "order_status": "0", "deliveries": [ { "delivery_id": "123123", "delivery_status": 0 }, { "delivery_id": "123124", "delivery_status": 1 } ], "applications":[ { "application_id": "123123","application_status": 0 }, { "application_id": "123124","application_status": 1 }] }
     * 
     */
        try {
            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
        } catch (\Laminas\Json\Exception\RuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
        
        $orderId = $result['data']['order_id'];
        $orderStatus = $result['data']['order_status'];
        if($result['data']['order_only']) {
            $clientOrder = $this->find(['order_id' => $orderId]);
            $clientOrder->setStatus($orderStatus);
            $this->persist($clientOrder, ['order_id' => $orderId]);
            return ['result' => true, 'description' => '', 'statusCode' => 200];
        }
        
        return ['result' => false, 'description' => 'not implemented', 'statusCode' => 405];
    }

}
