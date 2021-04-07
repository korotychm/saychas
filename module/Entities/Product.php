<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity
 */
class Product
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=12, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="provider_id", type="string", length=6, nullable=false)
     */
    private $providerId;

    /**
     * @var string
     *
     * @ORM\Column(name="category_id", type="string", length=9, nullable=false)
     */
    private $categoryId;

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
     * @ORM\Column(name="vendor_code", type="string", length=100, nullable=false)
     */
    private $vendorCode;

    /**
     * @var string
     *
     * @ORM\Column(name="param_value_list", type="text", length=65535, nullable=false)
     */
    private $paramValueList;

    /**
     * @var string
     *
     * @ORM\Column(name="param_variable_list", type="text", length=65535, nullable=false)
     */
    private $paramVariableList;

    /**
     * @var string
     *
     * @ORM\Column(name="brand_id", type="string", length=8, nullable=false)
     */
    private $brandId;


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
     * Set providerId.
     *
     * @param string $providerId
     *
     * @return Product
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
     * Set categoryId.
     *
     * @param string $categoryId
     *
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * Set vendorCode.
     *
     * @param string $vendorCode
     *
     * @return Product
     */
    public function setVendorCode($vendorCode)
    {
        $this->vendorCode = $vendorCode;

        return $this;
    }

    /**
     * Get vendorCode.
     *
     * @return string
     */
    public function getVendorCode()
    {
        return $this->vendorCode;
    }

    /**
     * Set paramValueList.
     *
     * @param string $paramValueList
     *
     * @return Product
     */
    public function setParamValueList($paramValueList)
    {
        $this->paramValueList = $paramValueList;

        return $this;
    }

    /**
     * Get paramValueList.
     *
     * @return string
     */
    public function getParamValueList()
    {
        return $this->paramValueList;
    }

    /**
     * Set paramVariableList.
     *
     * @param string $paramVariableList
     *
     * @return Product
     */
    public function setParamVariableList($paramVariableList)
    {
        $this->paramVariableList = $paramVariableList;

        return $this;
    }

    /**
     * Get paramVariableList.
     *
     * @return string
     */
    public function getParamVariableList()
    {
        return $this->paramVariableList;
    }

    /**
     * Set brandId.
     *
     * @param string $brandId
     *
     * @return Product
     */
    public function setBrandId($brandId)
    {
        $this->brandId = $brandId;

        return $this;
    }

    /**
     * Get brandId.
     *
     * @return string
     */
    public function getBrandId()
    {
        return $this->brandId;
    }
}
