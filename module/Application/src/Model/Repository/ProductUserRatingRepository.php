<?php

// src/Model/Repository/ProductUserRatingRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Json\Json;
//use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
//use Laminas\Db\Sql\Sql;
//use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\ProductUserRating;
use Application\Model\RepositoryInterface\ProductUserRatingRepositoryInterface;

class ProductUserRatingRepository extends Repository implements ProductUserRatingRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "product_user_rating";

    /**
     * @var Setting
     */
    protected ProductUserRating $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param ProductUserRating $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            ProductUserRating $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
    }

    /**
     * set rating product_user_rating and set average rating product_rating
     *
     *  @param type array
     */
    public function setProductRating ($param)
    {
        $query = "CALL set_product_rating('".$param["product_id"]."','".$param["user_id"]."','".$param["product_id"]."',);";
        return $res = $this->db->query($query)->execute();
        
    }
//    /**
//     * Adds given setting into it's repository
//     *
//     * @param json
//     */
//    public function replace($content)
//    {
//        try {
//            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
//        } catch (\Laminas\Json\Exception\RuntimeException $e) {
//            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
//        }
//
//        if ((bool) $result['truncate']) {
//            $this->db->query("truncate table {$this->tableName}")->execute();
//        }
//        
//        $data = $result['data'];
//        
//        $encodedData = Json::encode($data);
//        
//        $setting = new Setting();
//        
//        $setting->setId('main_menu');
//        $setting->setValue($encodedData);
//        
//        $this->persist($setting, ['id' => $setting->getId()]);
//        
//        return ['result' => true, 'description' => '', 'statusCode' => 200];
//    }

}
