<?php

// src/Model/Entity/UserData.php

namespace Application\Model\Entity;

use Application\Model\Repository\UserDataRepository;
use Application\Model\Traits\Searchable;
use Laminas\Json\Json;

/**
 * Description of UserData
 *
 * @author alex
 */
class UserData extends Entity
{

    /**
     * Behavior
     */
    use Searchable;

    /**
     * @var UserDataRepository
     */
    public static UserDataRepository $repository;

    /**
     * Get primary key name
     *
     * @return string|array
     */
    public function primaryKeyName()
    {
        return 'id';
    }

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
     * Length 36
     * @var UUID
     */
    protected $fias_id;

    /**
     * @var int
     */
    protected $fias_level;
    
    /**
     * @var int
     */
    protected $time = 0;

    /**
     * Parse json and return array or throw exception if not parsed
     * 
     * @param string $json
     * @return array
     * @throws Exception
     */
    private function parseJson($json)
    {
        try {
            return Json::decode($json, Json::TYPE_ARRAY);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
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
    public function setUserId($userId)
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
        $arr = $this->parseJson($geodata);
        $data = $arr['data'];
        $this->fias_id = $data['fias_id'];
        $this->fias_level = $data['fias_level'];
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
//    public function getTimestamp()
//    {
//        return $this->timestamp;
//    }

    public function receiveTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set timestamp
     *
     * @return $this
     */
//    public function setTimestamp($timestamp)
//    {
//        $this->timestamp = $timestamp;
//        return $this;
//    }

    /**
     * Set fias_id
     *
     * @param string $fiasId
     * @return $this
     */
    public function setFiasId($fiasId)
    {
        $this->fias_id = $fiasId;
        return $this;
    }

    /**
     * Get fias_id
     *
     * @return string
     */
    public function getFiasId()
    {
        return $this->fias_id;
    }

    /**
     * Set fias_level
     *
     * @param int $fiasLevel
     * @return $this
     */
    public function setFiasLevel($fiasLevel)
    {
        $this->fias_level = $fiasLevel;
        return $this;
    }

    /**
     * Get fias_level
     *
     * @return int
     */
    public function getFiasLevel()
    {
        return $this->fias_level;
    }
    
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }
    
    public function getTime()
    {
        return $this->time;
    }

}
