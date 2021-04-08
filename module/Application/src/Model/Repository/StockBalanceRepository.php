<?php
// src/Model/Repository/StockBalanceRepository.php

namespace Application\Model\Repository;

use InvalidArgumentException;
use RuntimeException;
// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Json\Json;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
//use Laminas\Db\Sql\Select;
//use Laminas\Db\Sql\Where;
use Application\Model\Entity\StockBalance;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;

class StockBalanceRepository extends Repository implements StockBalanceRepositoryInterface
{
    /**
     * @var string
     */
    protected $tableName="stock_balance";

    /**
     * @var StockBalance
     */
    protected StockBalance $prototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param StockBalance $prototype
     */
    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        StockBalance $prototype
    ) {
        $this->db            = $db;
        $this->hydrator      = $hydrator;
        $this->prototype = $prototype;
    }

    /**
     * Returns a list of stock_balances
     *
     * @return Entity[]
     */
    public function findAll($params)
    {
        $sql    = new Sql($this->db);
        $select = $sql->select($this->tableName);
        $select->columns(['*']);
        if(isset($params['order']))     { $select->order($params['order']); }
        if(isset($params['limit']))     { $select->limit($params['limit']); }
        if(isset($params['offset']))    { $select->offset($params['offset']); }
        if(isset($params['sequence']))  { $select->where(['id'=>$params['sequence']]); }
        $select->where(['store_id' => $params['store_id'], 'product_id' => $params['product_id']]);
        
//        $selectString = $sql->buildSqlString($select);
//        echo $selectString.'<br/>';
        
        $stmt   = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

 
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            return [];
        }
        
        $resultSet = new HydratingResultSet(
            $this->hydrator,
            $this->prototype
        );
        $resultSet->initialize($result);
        
        return $params['array'] == 1 ? $resultSet->toArray() : $resultSet;
    }

    /**
     * Adds given stock balance into it's repository
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
     * Delete stock balances specified by json array of objects
     * @param json
     */
    public function delete($json) {
        return ['result' => false, 'description' => 'Method is not supported: cannot delete stock balance', 'statusCode' => 405];
    }    
    
}