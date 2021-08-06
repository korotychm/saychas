<?php

// ControlPanel/src/Model/Entity/Delivery.php

namespace Application\Model\Entity;

use Application\Model\Entity\Entity;
use Application\Model\Repository\DeliveryRepository;
use Application\Model\Traits\Searchable;

/**
 * Description of ClientOrder
 *
 * @author alex
 */
class Delivery extends Entity
{
    use Searchable;
    
    public static DeliveryRepository $repository;

    /** @var int */
    protected $id;
    
    /** @var string */
    protected $delivery_id;

    /** @var string */
    protected $order_id = '';
    
    /** @var string */
    protected $delivery_info = '';
    
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
     * Set delivery_id
     *
     * @param string $deliveryId
     * @return $this
     */
    public function setDeliveryId($deliveryId)
    {
        $this->delivery_id = $deliveryId;
        return $this;
    }

    /**
     * Get delivery_id
     *
     * @return string
     */
    public function getDeliveryId()
    {
        return $this->delivery_id;
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
