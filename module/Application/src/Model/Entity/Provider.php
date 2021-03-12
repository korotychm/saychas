<?php

namespace Application\Model\Entity;

class Provider
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string | null
     */
    private $title;

    /**
     * @var string | null
     */
    private $description;

    /**
     * @var string | null
     */
    private $icon;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string | null $title
     * @return $this
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string | null $description
     * @return $this
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string | null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string | null $icon
     * @return $this
     */
    public function setIcon($icon = null)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return string | null
     */
    public function getIcon()
    {
        return $this->icon;
    }
}
