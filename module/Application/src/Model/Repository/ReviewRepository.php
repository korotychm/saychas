<?php

// src/Model/Repository/ReviewRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\Review;
use Application\Model\RepositoryInterface\ReviewRepositoryInterface;
use Application\Service\ImageHelperFunctionsService;
use Application\Helper\ArrayHelper;

class ReviewRepository extends Repository implements ReviewRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "review";

    /**
     * @var Review
     */
    protected Review $prototype;
    
    private $imageHelperFunctions;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Review $prototype
     * @param object $imageHelperFunctions
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            Review $prototype,
            ImageHelperFunctionsService $imageHelperFunctions
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
        $this->imageHelperFunctions = $imageHelperFunctions;
        
        parent::__construct();
    }
    
    /**
     * Adds given review into it's repository
     * @param json
     */
    public function replace($content)
    {
        try {
            $result = Json::decode($content, Json::TYPE_ARRAY);
        } catch (LaminasJsonRuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }

        if ((bool) $result['truncate']) {
            //$this->db->query("truncate table review")->execute();
            return ['result' => false, 'description' => 'Method is not supported: can`t truncate review', 'statusCode' => 405];
        }

        foreach ($result['data'] as $row) {
            $sql = sprintf("replace INTO `review`(`id`, `user_id`, `product_id`, `rating`, `user_name`, `seller_name`, `user_message`, `seller_message`, `time_created`,  `time_modified`)  VALUES ('%s', '%s', '%s','%s', '%s', '%s','%s', '%s', '%s','%s')", $row['id'], $row['user_id'], $row['product_id'], $row['rating'], $row['user_name'], $row['seller_name'], $row['user_message'], $row['seller_message'], $row['time_created'],  $row['time_modified'] );
            try {
                $query = $this->db->query($sql);
                $query->execute();
                $this->imageHelperFunctions->insertReviewImage($row['images']['view'], $row['id']);
            } catch (InvalidQueryException $e) {
                return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
            }
        }
        
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }
    
    /**
     * Delete review  and related review_image specified by JSON array of objects
     * @param JSON
     * @return array
     */
    public function delete($json)
    {
      // parent::delete($json);
        try {
            $result = Json::decode($json, Json::TYPE_ARRAY);
        } catch (LaminasJsonRuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
        
        $total = ArrayHelper::extractId($result, "id");  
//        $total = [];
//        
//        foreach ($result as $item) {
//            array_push($total, $item['id']);
//        }
//        
        $sql = new Sql($this->db);
        $delete = $sql->delete()->from($this->tableName)->where(['id' => $total]);
        $deleteImages = $sql->delete()->from("review_image")->where(['review_id' => $total]);
        $selectString = $sql->buildSqlString($delete);
        $selectStringImages = $sql->buildSqlString($deleteImages);
        
        try {
            $this->db->query($selectString, $this->db::QUERY_MODE_EXECUTE);
            $this->db->query($selectStringImages, $this->db::QUERY_MODE_EXECUTE);
            return ['result' => true, 'description' => '', 'statusCode' => 200];
        } catch (InvalidQueryException $e) {
            return ['result' => false, 'description' => "error executing $sql ", 'statusCode' => 418];
        }
    }

}
