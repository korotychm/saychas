<?php

// ControlPanel/src/Model/Entity/ClientOrder.php

namespace Application\Model\Entity;

use Application\Model\Entity\Entity;
use Application\Model\Repository\ClientOrderRepository;
use Application\Model\Traits\Searchable;

/**
 * Description of ClientOrder
 *
 * @author alex
 */
class ClientOrder extends Entity
{
    use Searchable;
    
    /**
     * Get primary key
     *
     * @return string|array
     */
    public function primaryKey()
    {
        return ['user_id', 'order_id'];
    }
    
    public static ClientOrderRepository $repository;

    /** @var int */
    protected $id;

    /** @var string */
    protected $order_id = '';
    
    /** @var int */
    protected $user_id;
    
    /** @var string */
    protected $basket_info = '';

    /** @var string */
    protected $delivery_info = '';
    
    /** @var string */
    protected $payment_info = '';
    
    /** @var int */
    protected $date_created;
    
    
    /** @var int */
    protected $status = 0;
    
    /** @var timestamp */
    protected $timestamp;

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
     * Set user_id
     *
     * @param int $id
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
        return $this;
    }

    /**
     * Get user_id
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    
    /**
     * Set order_id
     *
     * @param string $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        $this->order_id = $orderId;
        return $this;
    }

    /**
     * Get order_id
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * Set basket_info
     *
     * @param string $basketInfo
     * @return $this
     */
    public function setBasketInfo($basketInfo)
    {
        $this->basket_info = $basketInfo;
        return $this;
    }

    /**
     * Get basket_info
     *
     * @return string
     */
    public function getBasketInfo()
    {
        return $this->basket_info;
    }

    /**
     * Set delivery_info
     *
     * @param string $deliveryInfo
     * @return $this
     */
    public function setDeliveryInfo($deliveryInfo)
    {
        $this->delivery_info = $deliveryInfo;
        return $this;
    }

    /**
     * Get delivery_info
     *
     * @return string
     */
    public function getDeliveryInfo()
    {
        return $this->delivery_info;
    }

    /**
     * Set payment_info
     *
     * @param string $paymentInfo
     * @return $this
     */
    public function setPaymentInfo($paymentInfo)
    {
        $this->payment_info = $paymentInfo;
        return $this;
    }

    /**
     * Get payment_info
     *
     * @return string
     */
    public function getPaymentInfo()
    {
        return $this->payment_info;
    }

    /**
     * Set date_created
     *
     * @param int $dateCreated
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
     * @return int
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get timestamp
     *
     * @return timestamp
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
    
    /**
     * Set timestamp
     *
     * @param int $timestamp
     * @return $this
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

}
