<?php
// src/Model/Entity/PredefCharValue.php

namespace Application\Model\Entity;

//use Doctrine\ORM\Mapping as ORM;

/**
 * PredefCharValue
 *
 * @ORM\Table(name="predef_char_value")
 * @ORM\Entity
 */
class CharacteristicValue
{
//        "id": "000000008",
//        "title": "256 ГБ",
//        "characteristic_id": "000000008"
    /**
     * id
     * @var string, length=9
     */
    private $id;

    /**
     * @var string|null
     */
    private $title;

    /**
     * * characteristic_id
     * @var string, length=9
     */
    private $characteristicId;


    /**
     * Set predefined character value.
     *
     * @param string $id
     *
     * @return PredefCharValue
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * Get predefined character value.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return PredefCharValue
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set type.
     *
     * @param string characteristicId
     *
     * @return PredefCharValue
     */
    public function setCharacteristicId($characteristicId)
    {
        $this->characteristicId = $characteristicId;

        return $this;
    }

    /**
     * Get characteristicId.
     *
     * @return string
     */
    public function getCharacteristicId()
    {
        return $this->characteristicId;
    }
        
}
