<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Characteristic
 *
 * @ORM\Table(name="characteristic")
 * @ORM\Entity
 */
class Characteristic
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
     * @ORM\Column(name="category_id", type="string", length=9, nullable=false)
     */
    private $categoryId = '';

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="type", type="boolean", nullable=false, options={"default"="1"})
     */
    private $type = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="filter", type="boolean", nullable=false)
     */
    private $filter = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="group", type="boolean", nullable=false)
     */
    private $group = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false, options={"default"="1"})
     */
    private $sortOrder = 1;


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
     * Set categoryId.
     *
     * @param string $categoryId
     *
     * @return Characteristic
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId.
     *
     * @return string
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Characteristic
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
     * @param bool $type
     *
     * @return Characteristic
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return bool
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set filter.
     *
     * @param bool $filter
     *
     * @return Characteristic
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Get filter.
     *
     * @return bool
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Set group.
     *
     * @param bool $group
     *
     * @return Characteristic
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group.
     *
     * @return bool
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set sortOrder.
     *
     * @param int $sortOrder
     *
     * @return Characteristic
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder.
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}
