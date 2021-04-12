<?php
// src/Model/Repository/StockBalanceRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Json\Json;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
//use Laminas\Db\Sql\Select;
//use Laminas\Db\Sql\Where;
use Application\Model\Entity\ProductImage;
use Application\Model\RepositoryInterface\ProductImageRepositoryInterface;

class ProductImageRepository extends Repository implements ProductImageRepositoryInterface
{
    /**
     * @var string
     */
    protected $tableName="product_image";

    /**
     * @var ProductImage
     */
    protected ProductImage $prototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param ProductImage $prototype
     */
    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        ProductImage $prototype
    ) {
        $this->db            = $db;
        $this->hydrator      = $hydrator;
        $this->prototype = $prototype;
    }

    /**
     * Adds given product image into it's repository
     * 
     * @param json
     */
    public function replace($content)
    {        
        try {
            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
        }catch(\Laminas\Json\Exception\RuntimeException $e){
           return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
        
        foreach($result as $row) {
            $sql = sprintf("replace INTO `stock_balance`(`product_id`, `store_id`, `rest`) VALUES ( '%s', '%s', %u)",
                    $row['product_id'], $row['store_id'], $row['rest']);
            try {
                $query = $this->db->query($sql);
                $query->execute();
            }catch(InvalidQueryException $e){
                return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
            }
        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }
    
    /**
     * Delete product images specified by json array of objects
     * @param json
     */
    public function delete($json) {
        return ['result' => false, 'description' => 'Method is not supported: cannot delete product image', 'statusCode' => 405];
    }    
    
}