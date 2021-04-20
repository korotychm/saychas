<?php

// src/Model/Entity/Store.php

/*
 * Here comes the text of your license
 * Each line should be prefixed with  * 
 */

namespace Application\Model\Entity;

/**
 * Description of Size
 *
 * @author alex
 */
class Size extends Entity
{

    /** @var $id */
    protected ?string $id;

    /** @var $title */
    protected ?string $title;

    /**
     * Setter
     * 
     * @param ?string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Getter
     * 
     * @return ?string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setter
     * 
     * @param ?string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Getter
     * 
     * @return ?string
     */
    public function getTitle()
    {
        return $this->title;
    }

}
