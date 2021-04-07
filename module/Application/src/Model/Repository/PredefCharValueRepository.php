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

class PredefCharValueRepository extends Repository implements PredefCharValueRepositoryInterface
{
    /**
     * @var string
     */
    protected $tableName="predef_char_value";

    /**
     * @var PredefCharValue
     */
    protected PredefCharValue $prototype;
    
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