<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Tmp
 *
 * @ORM\Table(name="tmp")
 * @ORM\Entity
 */
class Tmp
{
    /**
     * @var string
     *
     * @ORM\Column(name="category_id", type="string", length=9, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $categoryId;


    /**
     * Get categoryId.
     *
     * @return string
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }
}
