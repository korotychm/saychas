<?php

// src/Model/Entity/CharacteristicValue.php

namespace Application\Model\Entity;

//use Doctrine\ORM\Mapping as ORM;

/**
 * CharacteristicValue
 *
 * @ORM\Table(name="characteristic_value")
 * @ORM\Entity
 */
class CharacteristicValue
{

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
    private $characteristic_id;

    /**
     * Set predefined character value.
     *
     * @param string $id
     *
     * @return CharacteristicValue
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
     * @return CharacteristicValue
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
     * @return CharacteristicValue
     */
    public function setCharacteristicId($characteristicId)
    {
        $this->characteristic_id = $characteristicId;

        return $this;
    }

    /**
     * Get characteristic_id.
     *
     * @return string
     */
    public function getCharacteristicId()
    {
        return $this->characteristic_id;
    }

}
