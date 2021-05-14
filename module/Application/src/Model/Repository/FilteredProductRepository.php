<?php

// src/Model/Repository/FilteredProductRepository.php

namespace Application\Model\Repository;

//use InvalidArgumentException;
//use RuntimeException;
// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Sql;
//use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Application\Model\Entity\FilteredProduct;
use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;

class FilteredProductRepository extends Repository implements FilteredProductRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "filtered_product"; //view="filtered_product";

    /**
     * @var FilteredProduct
     */
    protected FilteredProduct $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Product $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            FilteredProduct $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
    }

    public function filterProductsByStores($params = [])
    {
//SELECT DISTINCT
//    s.id,
//    s.provider_id,
//    s.title,
//    pr.id AS product_id,
//    pr.title AS product_title,
//    sb.rest
//FROM
//    store s
//INNER JOIN provider p ON
//    p.id = s.provider_id
//INNER JOIN product pr ON
//    pr.provider_id = s.provider_id
//LEFT JOIN stock_balance sb ON
//    sb.product_id = pr.id AND sb.store_id = s.id
//WHERE
//    s.id IN('000000005', '000000004');

        $sql = new Sql($this->db);
        $w = new Where();
        $w->in('s.id', $params);
        $select = new Select();
        $select->quantifier(Select::QUANTIFIER_DISTINCT);
        $select->from(['s' => 'store'])->columns(['id', 'provider_id', 'title'])
                ->join(['p' => 'provider'], 'p.id = s.provider_id', [], $select::JOIN_INNER)
                ->join(['pr' => 'product'], 'pr.provider_id = s.provider_id', ['product_id' => 'id', 'product_title' => 'title'], $select::JOIN_INNER)
                ->join(['sb' => 'stock_balance'], 'sb.product_id = pr.id AND sb.store_id = s.id', ['rest' => 'rest'], $select::JOIN_LEFT)
//                ->join(['pri' => 'price'], 'pri.product_id = pr.id AND p.provider = p.id', ['price' => 'price'], $select::JOIN_LEFT)
                ->order(['id ASC'])->where($w);
//        $selectString = $sql->buildSqlString($select);
//        print_r($selectString);
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

    public function replace($entity)
    {
        throw new \Exception($this->tableName . ': Not implemented exception');
    }

    public function delete($json)
    {
        throw new \Exception($this->tableName . ': Not implemented exception');
    }

}
