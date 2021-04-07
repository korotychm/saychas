<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * PredefCharValue
 *
 * @ORM\Table(name="predef_char_value")
 * @ORM\Entity
 */
class PredefCharValue
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=9, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id = '';

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title = '';

    /**
     * @var string
     *
     * @ORM\Column(name="characteristic_id", type="string", length=9, nullable=false)
     */
    private $characteristicId = '';


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
     * Set characteristicId.
     *
     * @param string $characteristicId
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
