<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Store
 *
 * @ORM\Table(name="store")
 * @ORM\Entity
 */
class Store
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
     * @var string
     *
     * @ORM\Column(name="provider_id", type="string", length=11, nullable=false)
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
     * @param string $providerId
     *
     * @return Store
     */
    public function setProviderId($providerId)
    {
        $this->providerId = $providerId;

        return $this;
    }

    /**
     * Get providerId.
     *
     * @return string
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
     * @return Store
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
     * @return Store
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
     * @return Store
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
     * @return Store
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
     * @return Store
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
     * @return Store
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
