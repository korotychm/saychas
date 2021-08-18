<?php

// src/Model/Entity/Setting.php

namespace Application\Model\Entity;

use Application\Model\Traits\Searchable;
use Application\Model\RepositoryInterface\SettingRepositoryInterface;

/**
 * Setting
 */
class Setting extends Entity
{

    use Searchable;
    
    public static SettingRepositoryInterface $repository;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    //protected $key;

    /**
     * @var string
     */
    protected $value;
    /**
     * Get id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set key.
     *
     * @param string $key
     *
     * @return Brand
     */
//    public function setKey($key)
//    {
//        $this->key = $key;
//
//        return $this;
//    }

    /**
     * Get key.
     *
     * @return string
     */
//    public function getKey()
//    {
//        return $this->key;
//    }

    /**
     * Set value.
     *
     * @param string $value
     *
     * @return 
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

}
