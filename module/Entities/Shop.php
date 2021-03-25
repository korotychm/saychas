<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Shop
 *
 * @ORM\Table(name="shop")
 * @ORM\Entity
 */
class Shop
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="provider_id", type="integer", nullable=false)
     */
    private $providerId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", length=65535, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", length=65535, nullable=false)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="geox", type="text", length=65535, nullable=false)
     */
    private $geox;

    /**
     * @var string
     *
     * @ORM\Column(name="geoy", type="text", length=65535, nullable=false)
     */
    private $geoy;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="text", length=65535, nullable=false)
     */
    private $icon;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set providerId.
     *
     * @param int $providerId
     *
     * @return Shop
     */
    public function setProviderId($providerId)
    {
        $this->providerId = $providerId;

        return $this;
    }

    /**
     * Get providerId.
     *
     * @return int
     */
    public function getProviderId()
    {
        return $this->providerId;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Shop
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
     * Set description.
     *
     * @param string $description
     *
     * @return Shop
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set address.
     *
     * @param string $address
     *
     * @return Shop
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set geox.
     *
     * @param string $geox
     *
     * @return Shop
     */
    public function setGeox($geox)
    {
        $this->geox = $geox;

        return $this;
    }

    /**
     * Get geox.
     *
     * @return string
     */
    public function getGeox()
    {
        return $this->geox;
    }

    /**
     * Set geoy.
     *
     * @param string $geoy
     *
     * @return Shop
     */
    public function setGeoy($geoy)
    {
        $this->geoy = $geoy;

        return $this;
    }

    /**
     * Get geoy.
     *
     * @return string
     */
    public function getGeoy()
    {
        return $this->geoy;
    }

    /**
     * Set icon.
     *
     * @param string $icon
     *
     * @return Shop
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon.
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }
}
