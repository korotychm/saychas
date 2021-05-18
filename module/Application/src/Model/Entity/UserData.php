<?php

// src/Model/Entity/UserData.php

namespace Application\Model\Entity;

/**
 * Description of UserData
 *
 * @author alex
 */
class UserData extends Entity
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $user_id;

    /**
     * @var text
     */
    protected $address;

    /**
     * @var text
     */
    protected $geodata;
    
    /**
     * @var timestamp
     */
    protected $timestamp;

    /**
     * Set id.
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
     * Get id.
     *
     * @return type
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user_id.
     *
     * @param int $userId
     * @return $this
     */
    public function setUserId(string $userId)
    {
        $this->user_id = $userId;
        return $this;
    }

    /**
     * Get user_id.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set address.
     *
     * @param string $address
     * @return $this
     */
    public function setAddress(string $address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set geodata.
     *
     * @param string $geodata
     * @return $this
     */
    public function setGeodata(string $geodata)
    {
        $this->geodata = $geodata;
        return $this;
    }

    /**
     * Get geodata.
     *
     * @return string
     */
    public function getGeodata()
    {
        return $this->geodata;
    }
    
    /**
     * Get timestamp
     * 
     * @return string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set timestamp
     * 
     * @return timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }
}
