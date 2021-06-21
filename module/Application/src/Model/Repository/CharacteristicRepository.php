<?php

// src/Model/Repository/CharacteristicRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Json\Json;
//use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Helper\ArrayHelper;
use Application\Model\Entity\Characteristic;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;

class CharacteristicRepository extends Repository implements CharacteristicRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "characteristic";
    protected $tableValuesName = "characteristic_value";

    /**
     * @var Characteristic
     */
    protected Characteristic $prototype;
    
//    protected $mclient;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Characteristic $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            Characteristic $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
//        $this->mclient = new \MongoDB\Client(
//            'mongodb://saychas:saychas@localhost/saychas'
//        );        
    }

    public const HEADER_TYPE = 0;
    public const STRING_TYPE = 1;
    public const INTEGER_TYPE = 2;
    public const BOOL_TYPE = 3;
    public const REFERENCE_TYPE = 4;
    public const PROVIDER_REFERENCE_TYPE = 5;
    public const BRAND_REFERENCE_TYPE = 6;
    public const COLOR_REFERENCE_TYPE = 7;

    /**
     * получаем массив характеристик
     * @param type $list
     * @return \Application\Model\Repository\HydratingResultSet
     */
    public function getCharacteristicFromList($list = 0, $param = [])
    {
        //if ($param['where']) $wereAppend = $param['where'];

        $query = "SELECT `v`.`title` AS val, `v`.`id` as val_id,  `tit`.* FROM `{$this->tableName}` AS tit INNER JOIN `{$this->tableValuesName}` AS v ON (`tit`.`id` = `v`.`characteristic_id`) WHERE FIND_IN_SET( `v`.`id`,'0,$list' ) " . $param['where'] . " and  `tit`.`filter` = 1  and tit.type not in (0, 1) ORDER BY `tit`.`sort_order` ";

//        exit ($query);
        
        //   if ($param['where']) exit($query );
        $result = $this->db->query($query)->execute();
        //exit (print_r($result));
        //SELECT     `val`.`title` AS val,     `tit`.* FROM     `characteristic` AS `tit` INNER JOIN     `characteristic_value` AS `val` ON     (`tit`.`id` = `val`.`characteristic_id`) WHERE     FIND_IN_SET(        `val`.`id`,        "000000001,000000002,000000003,000000004"    ) ORDER BY    `tit`.`sort_order`
        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            throw new \Exception('banzaii');
        }

        $resultSet = new HydratingResultSet(
                $this->hydrator,
                $this->prototype
        );
        $resultSet->initialize($result);

        return $resultSet;
    }

    public function persist($entity, $params, $hydrator = null)
    {
        if (null == $hydrator) {
            $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();

            $composite = new \Laminas\Hydrator\Filter\FilterComposite();
            $composite->addFilter(
                    'excludeval',
                    new \Laminas\Hydrator\Filter\MethodMatchFilter('getVal'),
                    \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND
            );
            $composite->addFilter(
                    'excludevalId',
                    new \Laminas\Hydrator\Filter\MethodMatchFilter('getValId'),
                    \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND
            );

            $hydrator->addFilter('excludes', $composite, \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND);
        }

        parent::persist($entity, $params, $hydrator);
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
        } catch (\Laminas\Json\Exception\RuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }

//        $this->mclient->saychas->characteristic->drop();
        $this->mclient->saychas->characteristic->insertMany($result['data']);

        if ((bool) $result['truncate']) {
            $this->db->query("truncate table characteristic")->execute();
        }

//        $q = ArrayHelper::groupBy($result['data'], 'category_id');
        $r = ArrayHelper::groupBy($result['data'], function ($item) {
                    return $item['category_id'];
                });

        $sql = new Sql($this->db);
        $delete = $sql->delete()->from('characteristic')->where(['category_id ' => array_keys($r)]);

        $statement = $sql->prepareStatementForSqlObject($delete);
        $statement->execute();

        foreach ($result['data'] as $row) {
//            $sql = sprintf("replace INTO `{$this->tableName}`(`id`, `category_id`, `title`, `type`, `sort_order`, `filter`, `group`, `unit`, `description`, `is_main`, `is_mandatory`, `is_list`) VALUES ( '%s', '%s', '%s', %u, %u, %u, %u, '%s', '%s', %u, %u, %u)",
//                    $row['id'], $row['category_id'], $row['title'], $row['type'], $row['sort_order'], $row['filter'], $row['group'], $row['unit'], '', $row['is_main'], $row['is_mandatory'], $row['is_list']);
            $sql = sprintf("replace INTO `{$this->tableName}`(`id`, `category_id`, `title`, `type`, `sort_order`, `filter`, `group`, `unit`, `description`, `is_main`, `is_mandatory`, `is_list`) VALUES ( '%s', '%s', '%s', %u, %u, %u, %u, '%s', '%s', %u, %u, %u)",
                    implode('-', [$row['id'], $row['category_id']]), $row['category_id'], $row['title'], $row['type'], $row['sort_order'], $row['filter'], $row['group'], $row['unit'], '', $row['is_main'], $row['is_mandatory'], $row['is_list']);
            try {
                $query = $this->db->query($sql);
                $query->execute();
            } catch (InvalidQueryException $e) {
                return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
            }
        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }

}
