<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="category", uniqueConstraints={@ORM\UniqueConstraint(name="id_1C_group", columns={"id_1C_group"})})
 * @ORM\Entity
 */
class Category
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_group", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="group_name", type="text", length=65535, nullable=false)
     */
    private $groupName;

    /**
     * @var int
     *
     * @ORM\Column(name="parent", type="integer", nullable=false)
     */
    private $parent;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", length=65535, nullable=false)
     */
    private $comment;

    /**
     * @var int
     *
     * @ORM\Column(name="id_1C_group", type="integer", nullable=false)
     */
    private $id1cGroup;


    /**
     * Get idGroup.
     *
     * @return int
     */
    public function getIdGroup()
    {
        return $this->idGroup;
    }

    /**
     * Set groupName.
     *
     * @param string $groupName
     *
     * @return Category
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;

        return $this;
    }

    /**
     * Get groupName.
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * Set parent.
     *
     * @param int $parent
     *
     * @return Category
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent.
     *
     * @return int
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set comment.
     *
     * @param string $comment
     *
     * @return Category
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set id1cGroup.
     *
     * @param int $id1cGroup
     *
     * @return Category
     */
    public function setId1cGroup($id1cGroup)
    {
        $this->id1cGroup = $id1cGroup;

        return $this;
    }

    /**
     * Get id1cGroup.
     *
     * @return int
     */
    public function getId1cGroup()
    {
        return $this->id1cGroup;
    }
}
