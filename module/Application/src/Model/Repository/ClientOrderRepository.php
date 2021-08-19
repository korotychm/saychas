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
use RuntimeException;
//use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
//use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\ClientOrder;
use Application\Model\Repository\Repository;

class ClientOrderRepository extends Repository
{
    protected const ORDER = 0;
    
    protected const DELIVERY = 1;
    
    protected const REQUISITION = 2;

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
    public function replace1($content)
    {
        /** @var JSON $content */

    /**
     * receive-client-order-statuses
        {
            "order_id": "123",
            "order_status": "0",
            "order_only": true,
            "deliveries": 
            [
                {
                    "delivery_id": "123123",
                    "delivery_status": 0
                    "requisitions": 
                    [
                        {
                            "requisition_id": "123123",
                            "requisition_status": 0
                        }, {
                            "requisition_id": "123124",
                            "requisition_status": 1
                        }
                    ]
                }, {
                    "delivery_id": "123124",
                    "delivery_status": 1
                }
            ],
        }
        { "orderId": "123", "order_status": "0", "deliveries": [ { "delivery_id": "123123", "delivery_status": 0 }, { "delivery_id": "123124", "delivery_status": 1 } ], "requisitions":[ { "requisition_id": "123123","requisition_status": 0 }, { "requisition_id": "123124","requisition_status": 1 }] }
        [
            {
              "orderId": "000000023",
              "order_status": "0",
              "deliveries": [
                {
                  "delivery_id": "000000000000000090",
                  "delivery_status": "0",
                  "requisitions": [
                    {
                      "requisition_id": "000000000000000133",
                      "requisition_status": "0"
                    },
                    {
                      "requisition_id": "000000000000000134",
                      "requisition_status": "0"
                    }
                  ]
                },
                {
                  "delivery_id": "000000000000000088",
                  "delivery_status": "1",
                  "requisitions": [
                    {
                      "requisition_id": "000000000000000130",
                      "requisition_status": "0"
                    }
                  ]
                },
                {
                  "delivery_id": "000000000000000087",
                  "delivery_status": "0",
                  "requisitions": [
                    {
                      "requisition_id": "000000000000000129",
                      "requisition_status": "0"
                    }
                  ]
                },
                {
                  "delivery_id": "000000000000000089",
                  "delivery_status": "0",
                  "requisitions": [
                    {
                      "requisition_id": "000000000000000131",
                      "requisition_status": "0"
                    },
                    {
                      "requisition_id": "000000000000000132",
                      "requisition_status": "0"
                    }
                  ]
                }
              ]
            }
        ]


        [{"orderId":"000000023","order_status":"0","deliveries":[{"delivery_id":"000000000000000090","delivery_status":"0","requisitions":[{"requisition_id":"000000000000000133","requisition_status":"0"},{"requisition_id":"000000000000000134","requisition_status":"0"}]},{"delivery_id":"000000000000000088","delivery_status":"1","requisitions":[{"requisition_id":"000000000000000130","requisition_status":"0"}]},{"delivery_id":"000000000000000087","delivery_status":"0","requisitions":[{"requisition_id":"000000000000000129","requisition_status":"0"}]},{"delivery_id":"000000000000000089","delivery_status":"0","requisitions":[{"requisition_id":"000000000000000131","requisition_status":"0"},{"requisition_id":"000000000000000132","requisition_status":"0"}]}]}]
        {"orders" :[] }


     * 
     * 
     */
        try {
            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
        } catch (\Laminas\Json\Exception\RuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
        
//        $flags['has_orders'] = count($result['data']) > 0;
//        $flags['has_deliveries'] = count($result['data']) > 0 && isset($result['data']['deliveries']);
//        $flags['has_requisitions'] = count($result['data']) > 0 && isset($result['data']['deliveries']) && isset($result['data']['deliveries']['requisitions']);
        
//        foreach($result['data'] as $order) {
//            print_r($order);
//        }
        $outOrder = null;
        $o = null;
        array_walk($result['data'], function($order) use (&$outOrder, &$o){
            // do order
            //print_r($order['order_id']);
            $orderId = $order['order_id'];
            $orderStatus = $order['order_status'];
            $clientOrder = $this->find(['order_id' => $orderId]);
            if(null == $clientOrder) {
                throw new RuntimeException('Order with specified order_id not found');
            }
            $clientOrder->setStatus($orderStatus);
//            $this->persist($clientOrder, ['order_id' => $orderId]);
            
            if(count($order['deliveries']) <= 0) {
                return;
            }
            array_walk($order['deliveries'], function($delivery) use ($clientOrder) {
                // do delivery
                $deliveryInfo = $clientOrder->getDeliveryInfo();
                $di = json_decode($deliveryInfo, true);
                foreach($di['deliveries'] as &$d) {
                    if($d['delivery_id'] == $delivery['delivery_id']) {
                        $d['delivery_status'] = $delivery['delivery_status'];
                    }
                }
                $clientOrder->setDeliveryInfo(json_encode($di, true));
                
                
                if(count($delivery['requisitions']) <= 0) {
                    return;
                }
                array_walk($delivery['requisitions'], function($requisition) use($delivery, $clientOrder, $di) {
                    // do requisition
                    $deliveryInfo = $clientOrder->getDeliveryInfo();
                    $di = json_decode($deliveryInfo, true);
                    foreach($di['deliveries'] as &$d) {
                        foreach($d['requisitions'] as &$r) {
                            if($r['requisition_id'] == $requisition['requisition_id']) {
                                $r['requisition_status'] = $requisition['requisition_status'];
                            }
                        }
                    }
                    $clientOrder->setDeliveryInfo(json_encode($di, true));
                });
            });
            $outOrder = $clientOrder;
            $o = $order;
            $this->persist($clientOrder, ['order_id' => $orderId]);
        });

        
        var_dump($outOrder);
                
        exit;
        
    }
    
    /**
     * Example
     * 
     *  [{"type":"0","order_id":"000000023","status":"0"},{"type":"0","order_id":"000000024","status":"1"},{"type":"1","order_id":"000000024","delivery_id":"000000000000000090","status":"0"},{"type":"1","order_id":"000000024","delivery_id":"000000000000000088","status":"0"},{"type":"2","order_id":"000000024","delivery_id":"000000000000000088","requisition_id":"000000000000000130","status":"1"},{"type":"2","order_id":"000000024","delivery_id":"000000000000000088","requisition_id":"000000000000000134","status":"1"}]
     * 
     * 
     * @param type $content
     * @return type
     */
    public function replace($content)
    {
        try {
            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
        } catch (\Laminas\Json\Exception\RuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
        
        foreach($result['data'] as $item) {
            $orderId = $item['order_id'];
            
            $clientOrder = $this->find(['order_id' => $orderId]);
            switch($item['type']) {
                case self::ORDER:
                default:
//                    $orderId = $item['order_id'];
                    $orderStatus = $item['status'];
                    $this->updateOrderStatus($orderId, $clientOrder, $orderStatus);
                    break;
                case self::DELIVERY:
//                    $orderId = $item['order_id'];
                    $deliveryId = $item['delivery_id'];
                    $deliveryStatus = $item['status'];
                    $this->updateDeliveryStatus($orderId, $clientOrder, $deliveryId, $deliveryStatus);
                    break;
                case self::REQUISITION:
//                    $orderId = $item['order_id'];
                    $deliveryId = $item['delivery_id'];
                    $requisitionId = $item['requisition_id'];
                    $requisitionStatus = $item['status'];
                    $this->updateRequisitionStatus($orderId, $clientOrder, $deliveryId, $requisitionId, $requisitionStatus);
                    break;
            }
        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }
    
    private function updateOrderStatus($orderId, $clientOrder, $orderStatus)
    {
        //$clientOrder = $this->find(['order_id' => $orderId]);
        if(null == $clientOrder) {
            throw new RuntimeException('Cannot find the order with given number');
        }
        $clientOrder->setStatus($orderStatus);
        $this->persist($clientOrder, ['order_id' => $orderId]);
    }
    
    private function updateDeliveryStatus($orderId, $clientOrder, $deliveryId, $deliveryStatus)
    {
        //$clientOrder = $this->find(['order_id' => $orderId]);
        if(null == $clientOrder) {
            throw new RuntimeException('Cannot find the order with given number');
        }
        $deliveryInfo = $clientOrder->getDeliveryInfo();
        $di = json_decode($deliveryInfo, true);
        foreach($di['deliveries'] as &$d) {
            if($d['delivery_id'] == $deliveryId) {
                $d['delivery_status'] = $deliveryStatus;
            }
        }
        $status = json_encode($di, true);
        $clientOrder->setDeliveryInfo($status);
        $this->persist($clientOrder, ['order_id' => $orderId]);
    }
    
    private function updateRequisitionStatus($orderId, $clientOrder, $deliveryId, $requisitionId, $requisitionStatus)
    {
        //$clientOrder = $this->find(['order_id' => $orderId]);
        if(null == $clientOrder) {
            throw new RuntimeException('Cannot find the order with given number');
        }
        $deliveryInfo = $clientOrder->getDeliveryInfo();
        $di = json_decode($deliveryInfo, true);
        foreach($di['deliveries'] as &$d) {
            foreach($d['requisitions'] as &$r) {
                if($d['delivery_id'] == $deliveryId) {
                    if($r['requisition_id'] == $requisitionId) {
                        $r['requisition_status'] = $requisitionStatus;
                    }
                }
            }
        }
        $status = json_encode($di, true);
        $clientOrder->setDeliveryInfo($status);
        $this->persist($clientOrder, ['order_id' => $orderId]);
    }
    

}






//        $orderId = $result['data']['order_id'];
//        $orderStatus = $result['data']['order_status'];
//        if(true === $result['data']['order_only']) {
//            $clientOrder = $this->find(['order_id' => $orderId]);
//            if(null == $clientOrder) {
//                throw new RuntimeException('Order cannot be null');
//            }
//            $clientOrder->setStatus($orderStatus);
//            $this->persist($clientOrder, ['order_id' => $orderId]);
//            return ['result' => true, 'description' => '', 'statusCode' => 200];
//        }elseif(false === $result['data']['order_only']){
//            
//        }
//        
//        throw new RuntimeException("order_only must be either true or false");
        








//            if(count($order['deliveries'] > 0)) {
//                array_walk($order['deliveries'], function($delivery) use ($order) {
//                    print_r('order_id = ' . $order['order_id'] . "\n");
//                    if(count($delivery['requisitions']) > 0) {
//                        array_walk($delivery['requisitions'], function($requisition) use($delivery) {
//                            print_r('delivery_id = ' . $delivery['delivery_id'] . "\n");
//                            print_r($requisition);
//                        });
//                    }
//                });
//            }
