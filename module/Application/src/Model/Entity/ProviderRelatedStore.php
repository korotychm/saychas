<?php

// src/Model/Entity/ProviderRelatedStore.php

namespace Application\Model\Entity;

//use Doctrine\ORM\Mapping as ORM;

use Application\Model\Repository\ProviderRepository;

/**
 * Store
 *
 * @ORM\Table(name="store")
 * @ORM\Entity
 */
class ProviderRelatedStore extends Entity
{

    /**
     * @var ProviderRepository
     */
    public static ProviderRepository $providerRepository;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="provider_id", type="string", length=11, nullable=false)
     */
    protected $provider_id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", length=65535, nullable=false)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", length=65535, nullable=false)
     */
    protected $address;

    /**
     * @var string
     *
     * @ORM\Column(name="geox", type="text", length=65535, nullable=false)
     */
    protected $geox;

    /**
     * @var string
     *
     * @ORM\Column(name="geoy", type="text", length=65535, nullable=false)
     */
    protected $geoy;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="text", length=65535, nullable=false)
     */
    protected $icon;

    /**
     * Get provider
     *
     * @return Provider
     * @throws \Exception
     */
    public function getProvider()
    {
        if (!( self::$providerRepository instanceof ProviderRepository )) {
            throw new \Exception('ProviderRepository expected; other type given');
        }
        return self::$providerRepository->findFirstOrDefault(['id' => $this->getProviderId()]);
    }

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
     * Set id.
     *
     * @param type $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set provider_id.
     *
     * @param string $provider_id
     *
     * @return Store
     */
    public function setProviderId($provider_id)
    {
        $this->provider_id = $provider_id;

        return $this;
    }

    /**
     * Get provider_id.
     *
     * @return string
     */
    public function getProviderId()
    {
        return $this->provider_id;
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
