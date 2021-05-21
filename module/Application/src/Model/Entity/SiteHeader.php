<?php

// src/Model/Entity/SiteHeader.php

namespace Application\Model\Entity;

/**
 * Description of SiteHeader
 *
 * @author alex
 */
class SiteHeader extends Entity
{

    /** @var $id */
    protected string $id;

    /** @var $category_id */
    protected string $category_id;

    /** @var $title */
    protected ?string $title;

    /** @var $index_number */
    protected ?int $index_number;

    public function __construct()
    {
        $this->title = null;
        $this->index_number = null;
    }

    /**
     * Setter
     *
     * @param string $id
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
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setter
     *
     * @param string $categoryId
     * @return $this
     */
    public function setCategoryId($categoryId)
    {
        $this->category_id = $categoryId;
        return $this;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getCategoryId()
    {
        return $this->category_id;
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

    /**
     * Setter
     *
     * @param ?int $indexNumber
     * @return $this
     */
    public function setIndexNumber($indexNumber)
    {
        $this->index_number = $indexNumber;
        return $this;
    }

    /**
     * Getter
     *
     * @return ?int
     */
    public function getIndexNumber()
    {
        return $this->index_number;
    }

}
