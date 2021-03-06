<?php

// src/Model/Repository/PriceRepository.php

namespace Application\Model\Repository;

//use InvalidArgumentException;
//use RuntimeException;
// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Json\Json;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\Price;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;

class PriceRepository extends Repository implements PriceRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "price";

    /**
     * @var Price
     */
    protected Price $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Price $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            Price $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
        
        parent::__construct();
    }

    /**
     * Returns a list of prices
     *
     * @return Price[]
     */
    public function findAll($params)
    {
        $sql = new Sql($this->db);
        $select = $sql->select($this->tableName);
        $select->columns(['*']);
        if (isset($params['order'])) {
            $select->order($params['order']);
        }
        if (isset($params['limit'])) {
            $select->limit($params['limit']);
        }
        if (isset($params['offset'])) {
            $select->offset($params['offset']);
        }
        if (isset($params['sequence'])) {
            $select->where(['id' => $params['sequence']]);
        }
        $select->where(['store_id' => $params['store_id'], 'product_id' => $params['product_id']]);

        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            return [];
        }

        $resultSet = new HydratingResultSet(
                $this->hydrator,
                $this->prototype
        );
        $resultSet->initialize($result);

        return $params['array'] == 1 ? $resultSet->toArray() : $resultSet;
    }
    
    public function findMinMaxPrice(array $products)
    {
        $sql = new Sql($this->db);
        $select = $sql->select($this->tableName);
        $columns = ["minprice" =>  new \Laminas\Db\Sql\Expression(" MIN(`new_price`) "),  "maxprice" =>  new \Laminas\Db\Sql\Expression(" MAX(`price`) "), ];
        $select->columns($columns);
        //$select->columns(['price']);
        $select->where(['product_id' => $products]);
        //$stmt = $sql->prepareStatementForSqlObject($select);
        $queryString = $sql->buildSqlString($select);
        
        $return = $this->db->query($queryString)->execute()->current();        
        $return["minprice"] = floor($return["minprice"]/100) * 100;
        $return["maxprice"] = ceil($return["maxprice"]/100) * 100;
        //exit (print_r($return));
        return $return;
        
     }

    /**
     * Adds given price into it's repository
     *
     * @param json
     */
    public function replace($content)
    {
        try {
            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
        } catch (\Laminas\Json\Exception\RuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }

//        $tableName = $this->tableName;
//        $this->mclient->saychas->$tableName->drop();
//        $this->mclient->saychas->$tableName->insertMany($result['data']);
        
        foreach ($result/*['data']*/ as $row) {
            
            $new_price = (int)($row['price'] - $row['price'] * $row['discount']/100 );
            $sql = sprintf("replace INTO `price`
                    (`product_id`, `store_id`, `reserve`, `unit`, `price`, `old_price`, `new_price`, `provider_id`, `discount`) 
                    VALUES ( '%s', '%s', %u, '%s', %u, %u, %u, '%s', %u)",
                    $row['product_id'], $row['store_id'], $row['reserve'], addslashes($row['unit']), $row['price'], $row['old_price'], $new_price, $row['provider_id'], $row['discount']);
            try {
                $query = $this->db->query($sql);
                $query->execute();
            } catch (InvalidQueryException $e) {
                //throw new \Exception($e->getMessage());
                return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
            }
        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }

    /**
     * Delete prices specified by json array of objects
     * @param $json
     */
    public function delete($json)
    {
        return ['result' => false, 'description' => 'Method is not supported: cannot delete price', 'statusCode' => 405];
    }

}
