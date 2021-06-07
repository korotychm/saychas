<?php

// src/Model/Entity/Store.php

namespace Application\Model\Entity;

use Application\Model\RepositoryInterface\ProviderRepositoryInterface;

/**
 * Store
 */
class Store extends Entity
{

    /**
     * @var ProviderRepository
     */
    public static ProviderRepositoryInterface $providerRepository;

    /**
     * @var string
     */
    protected $id;

    /**
     * @return Provider
     */
//    public function getProvider()
//    {
//        return self::$providerRepository->findFirstOrDefault(['id' => $this->getProviderId()]);
//    }

    /**
     * @var string
     */
    protected $provider_id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $address = '';

    /**
     * @var string
     */
    protected $geox = '';

    /**
     * @var string
     */
    protected $geoy = '';

    /**
     * @var string
     */
    protected $icon = '';

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
        $this->icon = null==$icon?'':$icon;

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
