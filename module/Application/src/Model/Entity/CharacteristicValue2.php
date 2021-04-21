<?php

// src/Model/Entity/CharacteristicValue2.php

namespace Application\Model\Entity;

//use Doctrine\ORM\Mapping as ORM;

/**
 * PredefCharValue
 *
 * @ORM\Table(name="characteristic_value2")
 * @ORM\Entity
 */
class CharacteristicValue2
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
     * Set character value.
     *
     * @param string $id
     *
     * @return CharacteristicValue2
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get character value.
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
     * @return CharacteristicValue2
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
     * @return CharacteristicValue2
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
