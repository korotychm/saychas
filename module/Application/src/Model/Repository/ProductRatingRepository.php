<?php

// src/Model/Repository/SettingRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Json\Json;
//use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
//use Laminas\Db\Sql\Sql;
//use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\ProductRating;
use Application\Model\RepositoryInterface\ProductRatingRepositoryInterface;

class ProductRatingRepository extends Repository implements ProductRatingRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "product_rating";

    /**
     * @var Setting
     */
    protected ProductRating $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param ProductRating $prototype
     */
    public function __construct(AdapterInterface $db, HydratorInterface $hydrator, ProductRating $prototype)
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
    }
    
    /**
     * Adds given product_rating into it's repository
     * 
     * @param Json $content
     * @return array
     */
    public function replace($content)
    {
        try {
            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
        } catch (\Laminas\Json\Exception\RuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }

        if ((bool) $result['truncate']) {
            $this->db->query("truncate table {$this->tableName}")->execute();
        }

        foreach ($result['data'] as $row) {
            $sql = sprintf("replace INTO `{$this->tableName}`(`product_id`, `rating`, `reviews`, `statistic`) VALUES ( '%s', '%s', '%s', '%s' )", $row['product_id'], $row['rating'], $row['reviews'], Json::encode($row['statistic']));

            try {
                $query = $this->db->query($sql);
                $query->execute();
            } catch (InvalidQueryException $e) {
                return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
            }
        }
       
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }
    
    /**
     * return error message 
     * 
     * @param JSON $json
     * @return array
     */
    public function delete($json)
    {
        return ['result' => false, 'description' => 'Method is not supported: can`t delete product rating', 'statusCode' => 405];
    }

}
