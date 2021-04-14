<?php
// src/Model/Repository/CharacteristicRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Helper\ArrayHelper;
use Application\Model\Entity\Characteristic;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;

class CharacteristicRepository extends Repository  implements CharacteristicRepositoryInterface
{
    /**
     * @var string
     */
    protected $tableName="characteristic";

    /**
     * @var Characteristic
     */
    protected Characteristic $prototype;
    
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
    
    public const STRING_TYPE = 1;
    
    public const INTEGER_TYPE = 2;
    
    public const BOOL_TYPE = 3;
    
    public const REFERENCE_TYPE = 4;

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
            $sql = sprintf("replace INTO `{$this->tableName}`(`id`, `category_id`, `title`, `type`, `sort_order`, `filter`, `group`, `unit`, `description`) VALUES ( '%s', '%s', '%s', %u, %u, %u, %u, '%s', '%s')",
                    $this->$row['id'], $row['category_id'], $row['title'], $row['type'], $row['sort_order'], $row['filter'], $row['group'], '', '');
//            $sql = "replace INTO `characteristic`(`id`, `category_id`, `title`, `type`, `sort_order`, `filter`, `group`) VALUES ( :id, :category_id, :title, :type, :sort_order, :filter, :group)";
              //$sql = "replace INTO `characteristic`(`id`, `category_id`, `title`, `type`, `sort_order`, `filter`, `group`) VALUES ( :id='?', :category_id=>'?', :title='?', :type=?, :sort_order=?, :filter=?, :group=?)";
//            print_r($sql);
//            echo "\n";
//            exit;
            try {
                //$query = $this->db->query($sql, [ $row['id'], $row['category_id'], $row['title'], $row['type'], $row['sort_order'], $row['filter'], $row['group'] ]);
                $query = $this->db->query($sql);
                //$query->execute([':id'=>$row['id'], ':category_id'=>$row['category_id'], ':title'=>$row['title'], ':type'=>$row['type'], ':sort_order'=>$row['sort_order'], ':filter'=>$row['filter'], ':group'=>$row['group'] ]);
//                $query->execute(['id'=>$row['id'], 'category_id'=>$row['category_id'], 'title'=>$row['title'], 'type'=>$row['type'], 'sort_order'=>$row['sort_order'], 'filter'=>$row['filter'], 'group'=>$row['group'] ]);
                $query->execute();
            }catch(InvalidQueryException $e){
                return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
            }
        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }

}