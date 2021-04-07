<?php
// src/Model/Repository/PredefCharValueRepository.php

namespace Application\Model\Repository;

use InvalidArgumentException;
use RuntimeException;
// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\PredefCharValue;
use Application\Model\RepositoryInterface\PredefCharValueRepositoryInterface;

class PredefCharValueRepository implements PredefCharValueRepositoryInterface
{
    /**
     * @var string
     */
    protected $tableName="predef_char_value";

    /**
     * @var AdapterInterface
     */
    private AdapterInterface $db;

    /**
     * @var HydratorInterface
     */
    private HydratorInterface $hydrator;

    /**
     * @var PredefCharValue
     */
    private PredefCharValue $prototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param PredefCharValue $prototype
     */
    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        PredefCharValue $prototype
    ) {
        $this->db            = $db;
        $this->hydrator      = $hydrator;
        $this->prototype = $prototype;
    }

    /**
     * Returns a list of prices
     *
     * @return Characteristic[]
     */
    public function findAll($params)
    {
        $sql    = new Sql($this->db);
        $select = $sql->select($this->tableName);
        if(isset($params['order']))     { $select->order($params['order']); }
        if(isset($params['limit']))     { $select->limit($params['limit']); }
        if(isset($params['offset']))    { $select->offset($params['offset']); }
        if(isset($params['sequence']))  { $select->where(['id'=>$params['sequence']]); }
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
        return $resultSet;
    }

    /**
     * Returns a single predefined character value.
     *
     * @param  array $params
     * @return PredefCharValue
     */    
    public function find($params)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select('predef_char_value');
        $select->where(['id = ?' => $params['id']]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                'Failed retrieving test with identifier "%s"; unknown database error.',
                $params['id']
            ));
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($result);
        $current = $resultSet->current();

        if (! $current) {
            throw new InvalidArgumentException(sprintf(
                'PredefCharValue with identifier "%s" not found.',
                $params['id']
            ));
        }

        return $current;
    }
    
    /**
     * Adds given characteristic into it's repository
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

        if((bool) $result['truncate']) {
            $this->db->query("truncate table predef_char_value")->execute();
        }

        foreach($result['data'] as $row) {
            $sql = sprintf("replace INTO `predef_char_value`(`id`, `title`, `characteristic_id`) VALUES ( '%s', '%s', '%s')",
                    $row['id'], $row['title'], $row['characteristic_id']);
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
     * Delete predefined characteristic values specified by json array of objects
     * @param $json
     */
    public function delete($json) {
        try {
            $result = Json::decode($json, Json::TYPE_ARRAY);
        }catch(LaminasJsonRuntimeException $e){
           return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
        $total = [];
        foreach ($result as $item) {
            array_push($total, $item['id']);
        }
        $sql    = new Sql($this->db);
        $delete = $sql->delete()->from('predef_char_value')->where(['id' => $total]);

        $selectString = $sql->buildSqlString($delete);
        try {
            $this->db->query($selectString, $this->db::QUERY_MODE_EXECUTE);
            return ['result' => true, 'description' => '', 'statusCode' => 200];
        }catch(InvalidQueryException $e){
            return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
        }
    }
    
}