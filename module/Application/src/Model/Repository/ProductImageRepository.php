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
    public function replace($data)
    {
        foreach($data as $product) {
            foreach($product->images as $image) {
                $sql = sprintf("replace INTO `product_image`(`product_id`, `ftp_url`, `sort_order`) VALUES ( '%s', '%s', %u )",
                        $product->id, $image, 0);
                try {
                    //'SELECT * FROM `artist` WHERE `id` = ?', [5]
//                    $sql = sprintf("replace INTO `product_image`(`product_id`, `ftp_url`, `sort_order`) VALUES ( '%s', '%s', %u )",
//                        $product->id,$image, '', 0);
                    $query = $this->db->query($sql);
                    $query->execute();
                }catch(Exception $e){
                    return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
                }
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