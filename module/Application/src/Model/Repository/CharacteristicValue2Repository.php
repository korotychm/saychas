<?php
// src/Model/Repository/CharacteristicValue2Repository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Json\Json;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\CharacteristicValue2;
use Application\Model\RepositoryInterface\CharacteristicValue2RepositoryInterface;

class CharacteristicValue2Repository extends Repository implements CharacteristicValue2RepositoryInterface
{
    /**
     * @var string
     */
    protected $tableName="characteristic_value2";

    /**
     * @var CharacteristicValue2
     */
    protected CharacteristicValue2 $prototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param CharacteristicValue2 $prototype
     */
    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        CharacteristicValue2 $prototype
    ) {
        $this->db            = $db;
        $this->hydrator      = $hydrator;
        $this->prototype = $prototype;
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
            $this->db->query("truncate table {$this->tableName}")->execute();
        }

        foreach($result['data'] as $row) {
            $sql = sprintf("replace INTO `{$this->tableName}`(`id`, `title`, `characteristic_id`) VALUES ( '%s', '%s', '%s')",
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
    
}