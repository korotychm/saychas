<?php
// src/Model/Repository/CharacteristicRepository.php

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
use Application\Helper\ArrayHelper;
use Application\Model\Entity\Characteristic;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;

class CharacteristicRepository implements CharacteristicRepositoryInterface
{
    /**
     * @var AdapterInterface
     */
    private AdapterInterface $db;

    /**
     * @var HydratorInterface
     */
    private HydratorInterface $hydrator;

    /**
     * @var Characteristic
     */
    private Characteristic $prototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Characteristic $prototype
     */
    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        Characteristic $prototype
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
    public function findAll($params=[])
    {
        $sql    = new Sql($this->db);
        $select = $sql->select('characteristic');
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
     * Returns a single characteristic.
     *
     * @param  array $params
     * @return Characteristic
     */    
    public function find($params)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select('characteristic');
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
                'Characteristic with identifier "%s" not found.',
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
            $this->db->query("truncate table characteristic")->execute();
        }
        
//        $q = ArrayHelper::groupBy($result['data'], 'category_id');
        $r = ArrayHelper::groupBy($result['data'], function($item) {
            return $item['category_id'];
        });
        
        $sql    = new Sql($this->db);
        $delete = $sql->delete()->from('characteristic')->where(['category_id ' => array_keys($r)]);

        $statement = $sql->prepareStatementForSqlObject($delete);
        $statement->execute();
        
        foreach($result['data'] as $row) {
            $sql = sprintf("replace INTO `characteristic`(`id`, `category_id`, `title`, `type`, `sort_order`, `filter`, `group`) VALUES ( '%s', '%s', '%s', %u, %u, %u, %u)",
                    $row['id'], $row['category_id'], $row['title'], $row['type'], $row['sort_order'], $row['filter'], $row['group']);
            //$sql = "replace INTO `characteristic`(`id`, `category_id`, `title`, `type`, `sort_order`, `filter`, `group`) VALUES ( :id, :category_id, :title, :type, :sort_order, :filter, :group)";
            
            try {
                $query = $this->db->query($sql);
                //$query->execute([':id'=>$row['id'], ':category_id'=>$row['category_id'], ':title'=>$row['title'], ':type'=>$row['type'], ':sort_order'=>$row['sort_order'], ':filter'=>$row['filter'], ':group'=>$row['group'] ]);
                $query->execute();
            }catch(InvalidQueryException $e){
                return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
            }
        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }
    
    /**
     * Delete prices specified by json array of objects
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
        $delete = $sql->delete()->from('characteristic')->where(['id' => $total]);

        $selectString = $sql->buildSqlString($delete);
        try {
            $this->db->query($selectString, $this->db::QUERY_MODE_EXECUTE);
            return ['result' => true, 'description' => '', 'statusCode' => 200];
        }catch(InvalidQueryException $e){
            return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
        }
    }
    
}