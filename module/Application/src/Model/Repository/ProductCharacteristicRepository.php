<?php

// src/Model/Repository/ProductCharacteristicRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Json\Json;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\ProductCharacteristic;
use Application\Model\RepositoryInterface\ProductCharacteristicRepositoryInterface;

class ProductCharacteristicRepository extends Repository implements ProductCharacteristicRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "product_characteristic";

    /**
     * @var ProductCharacteristic
     */
    protected ProductCharacteristic $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param ProductCharacteristic $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            ProductCharacteristic $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
    }

     /**
     * Get given ProductCharacteristic for  categotyTree
     *
     * @param array
     */
    public function getCategoryFilter ($categoryTree)
    {
        if ($categoryTree and count($categoryTree)) {
            foreach ($categoryTree as $category) $tree[] = $category[0];         
            
            $catgoryIn=join(",",$tree); //GROUP_CONCAT(a.`value`)
            $sql="SELECT b.`type` as type, b.`title` as `tit`, b.unit, a.`characteristic_id` as id , GROUP_CONCAT(a.`value`) as `val` "
                . "FROM `product_characteristic` as a "
                . "inner join characteristic as b on (a.`characteristic_id` = b.id) "
                . "WHERE `product_id` in (SELECT `id` FROM `product` WHERE `category_id` in ($catgoryIn)) "
                . "and b.`filter`=1  group by `characteristic_id` ";
 
            //exit ($sql);
            try {
                  $res = $this->db->query($sql, $this->db::QUERY_MODE_EXECUTE);
                  return $res;
            } 
            catch (InvalidQueryException $e) {return "error executing $sql ".print_r($e,true);}  
        }
    }   
    
    /**
     * Adds given ProductCharacteristic into it's repository
     *
     * @param json
     */
    public function replace($content)
    {
        return ['result' => false, 'description' => '', 'statusCode' => 405];
    }

}
