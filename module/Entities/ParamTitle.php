<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ParamTitle
 *
 * @ORM\Table(name="param_title")
 * @ORM\Entity
 */
class ParamTitle
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
     * @ORM\Column(name="title", type="text", length=255, nullable=false)
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="filter", type="integer", nullable=false)
     */
    private $filter = '0';


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
     * Set title.
     *
     * @param string $title
     *
     * @return ParamTitle
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
     * Set sortOrder.
     *
     * @param int $sortOrder
     *
     * @return ParamTitle
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

    /**
     * Set filter.
     *
     * @param int $filter
     *
     * @return ParamTitle
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Get filter.
     *
     * @return int
     */
    public function getFilter()
    {
        return $this->filter;
    }
}
