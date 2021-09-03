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
use Application\Resource\Resource;
use Application\Service\AcquiringCommunicationService;

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
    
    protected AcquiringCommunicationService $acquiringService;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param ClientOrder $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            ClientOrder $prototype,
            AcquiringCommunicationService $acquiringService
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
        $this->acquiringService = $acquiringService;

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
    

    /**
     * Adds given client_order into it's repository
     *
     * @param json
     */
    public function replace1($content)
    {
        /** @var JSON $content */

        try {
            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
        } catch (\Laminas\Json\Exception\RuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
        
        $outOrder = null;
        $o = null;
        array_walk($result['data'], function($order) use (&$outOrder, &$o){
            // do order
            $orderId = $order['order_id'];
            $orderStatus = $order['order_status'];
            $clientOrder = $this->find(['order_id' => $orderId]);
            if(null == $clientOrder) {
                throw new RuntimeException('Order with specified order_id not found');
            }
            $clientOrder->setStatus($orderStatus);
            
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

    }
    
    private function cancelOrder($clientOrder)
    {
        //$this->acquiringService->addCustomerTinkoff($args);
        $paymentInfo = Json::decode( $clientOrder->getPaymentInfo(), Json::TYPE_ARRAY);
        if (!empty($paymentInfo["PaymentId"])){
                $arg = ["PaymentId" => $paymentInfo["PaymentId"], "TerminalKey" => $paymentInfo["TerminalKey"]];
                $tinkoffData = $this->acquiringService->cancelTinkoff($args);
                mail("d.sizov@saychas.ru", "odercancel.log", print_r($tinkoffData, true)); // лог на почту
                return true;
        }
        return false;
    }


    /**
     * Replace
     * 
     * Example
     * 
     *  [{"type":"0","order_id":"000000023","status":"0"},{"type":"0","order_id":"000000024","status":"1"},{"type":"1","order_id":"000000024","delivery_id":"000000000000000090","status":"0"},{"type":"1","order_id":"000000024","delivery_id":"000000000000000088","status":"0"},{"type":"2","order_id":"000000024","delivery_id":"000000000000000088","requisition_id":"000000000000000130","status":"1"},{"type":"2","order_id":"000000024","delivery_id":"000000000000000088","requisition_id":"000000000000000134","status":"1"}]
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
            if(null == $clientOrder) {
                // throw new RuntimeException('Cannot find the order with given number');
                return ['result' => true, 'description' => 'Cannot find the order with given number', 'statusCode' => 200];
            }
            switch($item['type']) {
                case self::ORDER:
                default:
                    $orderStatus = $item['status'];
                    if ($orderStatus == 4 /*Resource::ORDER_STATUS_CODE_CANCELED*/) {
                        return ['result' => true, 'description' => 'не записалось!', 'statusCode' => 200];
                        $this->cancelOrder($clientOrder);
                    }
                    $this->updateOrderStatus($orderId, $clientOrder, $orderStatus);
                    break;
                case self::DELIVERY:
                    $deliveryId = $item['delivery_id'];
                    $deliveryStatus = $item['status'];
                    $this->updateDeliveryStatus($orderId, $clientOrder, $deliveryId, $deliveryStatus);
                    break;
                case self::REQUISITION:
                    $deliveryId = $item['delivery_id'];
                    $requisitionId = $item['requisition_id'];
                    $requisitionStatus = $item['status'];
                    $this->updateRequisitionStatus($orderId, $clientOrder, $deliveryId, $requisitionId, $requisitionStatus);
                    break;
            }
        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }
    
    /**
     * Update order status
     * 
     * @param type $orderId
     * @param type $clientOrder
     * @param type $orderStatus
     */
    private function updateOrderStatus($orderId, $clientOrder, $orderStatus)
    {
        $clientOrder->setStatus($orderStatus);
        $this->persist($clientOrder, ['order_id' => $orderId]);
    }
    
    /**
     * Update delivery status
     * 
     * @param type $orderId
     * @param type $clientOrder
     * @param type $deliveryId
     * @param type $deliveryStatus
     */
    private function updateDeliveryStatus($orderId, $clientOrder, $deliveryId, $deliveryStatus)
    {
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
    
    /**
     * Update requisition status
     * 
     * @param type $orderId
     * @param type $clientOrder
     * @param type $deliveryId
     * @param type $requisitionId
     * @param type $requisitionStatus
     */
    private function updateRequisitionStatus($orderId, $clientOrder, $deliveryId, $requisitionId, $requisitionStatus)
    {
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
