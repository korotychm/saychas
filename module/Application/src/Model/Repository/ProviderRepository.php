<?php

// src/Model/Repository/ProviderRepository.php

namespace Application\Model\Repository;

use InvalidArgumentException;
use RuntimeException;
// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
//use Laminas\Db\ResultSet\ResultSet;
//use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Json\Json;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\Provider;
use Application\Model\RepositoryInterface\ProviderRepositoryInterface;

class ProviderRepository extends Repository implements ProviderRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "provider";

    /**
     * @var Provider
     */
    protected Provider $prototype;
    
    /**
     * @var laminas.entity.manager
     */
    protected $entityManager;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Provider $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            Provider $prototype,
            $entityManager
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
        $this->entityManager = $entityManager;
        
        parent::__construct();
    }

    /**
     * Returns a list of providers from only availble stores,  width limit and order
     *
     * @return Provider[]
     */
//   public function findAvailableProviders ($param,$order="id ASC", $limit=100, $offset=0 )
    public function findAvailableProviders($params)
    {
        $sql = new Sql($this->db);
        if ($params['sequence']) {
            $subSelectAvailbleStore = $sql->select('store');
            $subSelectAvailbleStore->columns(['provider_id']);
            $subSelectAvailbleStore->where->in('id', $params['sequence']);
        }

        $select = $sql->select('provider');
        $select->columns(['*']);
        if ($params['sequence']) {
            $select->where->in('id', $subSelectAvailbleStore);
        }

        //$select -> where(["id in ?" => (new Select())->columns(["provider_id"])->from("store")->where($where)]);

        $select->order($params['order']);
        $select->limit($params['limit']);
        $select->offset($params['offset']);

//        $selectString = $sql->buildSqlString($select);
//        echo $selectString;
//        exit;

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
     * Adds given provider into it's repository
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
        
        if ((bool) $result['truncate']) {
            $this->db->query("truncate table provider")->execute();
        }

        foreach ($result['data'] as $row) {
            $sql = sprintf("replace INTO `provider`( `id`, `title`, `description`, `image`) VALUES ( '%s', '%s', '%s', '%s' )", $row['id'], $row['title'], $row['description'], $row['image']);
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
