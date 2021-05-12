<?php

// src/Model/Repository/StoreRepository.php

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
//use Laminas\Db\TableGateway\TableGateway;
//use Laminas\Db\Sql\ExpressionInterface;
//use Laminas\Db\Sql\Predicate;
//use Laminas\Db\Sql\Predicate\PredicateSet;
//use Laminas\Db\Sql\Predicate\In;
use Laminas\Db\Sql\Where;
use Application\Model\Entity\Store;
use Application\Model\RepositoryInterface\StoreRepositoryInterface;

class StoreRepository extends Repository implements StoreRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "store";

    /**
     * @var Store
     */
    protected Store $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Store $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            Store $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
    }

    /**
     * Function finds available stores of a specific provider
     *
     * @param int $providerId
     * @param array $param
     * @return Store[]
     */
    public function findStoresByProviderIdAndExtraCondition($providerId, $param)
    {
        $sql = new Sql($this->db);

        $where = new Where();
        $where->equalTo('provider_id', $providerId);
        // $where->in('id', $param);

        $select = $sql->select()->from('store')->columns(["id", "provider_id", "title", "description", "address", "geox", "geoy", "icon"])->where($where);

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
        return $resultSet;
    }

    /**
     * Adds given store into it's repository
     *
     * @param json $content
     */
    public function replace($content)
    {
        try {
            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
        } catch (\Laminas\Json\Exception\RuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }

        if ((bool) $result['truncate']) {
            $this->db->query("truncate table store")->execute();
        }

        foreach ($result['data'] as $row) {
            $sql = sprintf("replace INTO `store`( `id`, `provider_id`, `title`, `description`, `address`, `geox`, `geoy`, `icon`) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )",
                    $row['id'], $row['provider_id'], $row['title'], $row['description'], $row['address'], $row['geox'], $row['geoy'], $row['icon']);
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
