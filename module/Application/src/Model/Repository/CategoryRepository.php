<?php

// src/Model/Repository/CategoryRepository.php

namespace Application\Model\Repository;

use InvalidArgumentException;
use RuntimeException;
// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
//use Application\Model\Repository\Repository;
//use Laminas\Db\TableGateway\TableGateway;
use Application\Model\Entity\Category;
use Laminas\Db\Sql\Sql;
use Application\Helper\ArrayHelper;

//use Doctrine\ORM\Mapping as ORM;
//use Doctrine\Laminas\Hydrator\DoctrineObject as DoctrineHydrator;

class CategoryRepository /*extends Repository*/ implements CategoryRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "category";

    /**
     * @var AdapterInterface
     */
    private AdapterInterface $db;

    /**
     * @var HydratorInterface
     */
    private HydratorInterface $hydrator;
    
    protected $mclient;

    /**
     * @var Category
     */
    private Category $prototype;

    /**
     * @var string
     */
    private string $username;

    /**
     *
     * @var string
     */
    private string $password;
    
    private $cache;
    
    private $categories;

    /**
     * @param Adapter $db
     * @param HydratorInterface $hydrator
     * @param Category $prototype
     * @param string $username
     * @param string $password
     */
    public function __construct(
//        AdapterInterface $db,
            AdapterInterface $db,
            HydratorInterface $hydrator,
            Category $prototype,
            string $username,
            string $password,
            $cache
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
        $this->username = $username;
        $this->password = $password;
        $this->cache = $cache;
        
        $this->mclient = new \MongoDB\Client(
            'mongodb://saychas:saychas@localhost/saychas'
        );        
    }

    public function findAllCategories($echo = '', $i = 0, $idActive, $forceCreate = false): string
    {
        if ($forceCreate) {
            $this->cache->removeItem('category_container');
        }

        $result = false;
        $this->categories = $this->cache->getItem('category_container', $result);
        if(!$result) {
            $this->categories = $this->findAllCategories1($echo = '', $i = 0, $idActive);      
            $this->cache->setItem('category_container', $this->categories);
        }
        return $this->categories;
    }
    public function categoryTree($echo = '', $i = 0, $idActive): array
    {
        $sql = new Sql($this->db);
        $select = $sql->select();
        $select->from($this->tableName);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new HydratingResultSet(
                $this->hydrator,
                new Category('', 0, 0)
        );
        $resultSet->initialize($result);
        
        $results = $resultSet->toArray();
        
        /**/// !-- plusweb
        $categoriesHasProduct = $this->categoriesHasProduct();
        $newTree = [];
        foreach ($results as $value) {
            $newTree[$value['parent_id']][] = $value;
        }
        $tree = ArrayHelper::filterTree($newTree, $i, $categoriesHasProduct);
        /**/// plusweb --!
        
        //$tree = ArrayHelper::buildTree($result, $i); // !-- alex --!
        return $tree;
    }
    private function categoriesHasProduct ()
    {
        $query = "SELECT `id` FROM `category` WHERE `id` in (SELECT `category_id` FROM `product` WHERE 1 group by `category_id`)";
        $result = $this->db->query($query)->execute();
        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            throw new \Exception('no legal categories');
        }
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $res = $resultSet->initialize($result);//->toArray();
        foreach ($res as $row) {
            $return[$row->getId()] = true;
        }
        return (empty($return)) ? [] : $return;
    }
        
    /**
     * Return a string that contains html ul list
     *
     * @return string
     */
    private function findAllCategories1($echo = '', $i = '0', $idActive = false)
    {
        $sql = new Sql($this->db);
        $select = $sql->select();
        $select->from($this->tableName);
        $select->where(['parent_id' => $i]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        if ($i) {
            $echo .= "<ul>";
        }
        /** TODO: to be fixed later */
        foreach ($results as $result) {
            if (true /*             * || pubtv(id_1C_group) */) { // если в ветке есть хоть один товар, надо функцию сделать тоже такую

                ($idActive == $result['id']) ? $class = "class='open activ activecategoty'" : $class = "";
                $groupName = stripslashes($result['title']);
                //$echo.="<li><a href=#/catalog/".$result['id_1C_group']."  >$groupName</a>";
                $echo .= "<li $class><a href=/catalog/" . $result['id'] . "  >$groupName</a>";
                $echo = $this->findAllCategories1($echo, $result['id'], $idActive);
                $echo .= "</li>";
            }
        }
        if ($i) {
            $echo .= "</ul>";
        }

        return str_replace("<ul></ul>", "", $echo);
    }

    /**
     * Reads all data from category table
     * @return HydratingResultSet
     */
    public function readAll($param)
    {
        $sql = new Sql($this->db);
        $select = $sql->select($this->tableName)->columns(['*']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

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
     * Builds tree out of read data(see readAll)
     * @params['id'] - first element to start from
     * @param array $params
     * @return array
     */
//    public function findAll($params)
//    {
//        $data = $this->readAll();
//        return ArrayHelper::buildTree($data->toArray(), $params['id']);
//    }
////
    
     public function findAll($params)
    {
        $sql = new Sql($this->db);
        $select = $sql->select($this->tableName);
        if (isset($params['columns'])) {
            $select->columns($params['columns']);
        }
        if (isset($params['order'])) {
            $select->order($params['order']);
        }
        if (isset($params['limit'])) {
            $select->limit($params['limit']);
        }
        if (isset($params['offset'])) {
            $select->offset($params['offset']);
        }
        
        if (isset($params['group'])) {
            $select->group($params['group']);
        }
        
        if (isset($params['having'])) {
            $select->having($params['having']);
        }
        
        if (isset($params['where'])) {
            $select->where($params['where']);
        }
        if (isset($params['sequence'])) {
            $select->where(['id' => $params['sequence']]);
        }
        if (isset($params['joins'])) {
            $joins = $params['joins']->getJoins();
            foreach($joins as $join) {
                $select->join($join['name'], $join['on'], $join['columns'], $join['type'] );
            }
        }

        $stmt = $sql->prepareStatementForSqlObject($select);
        $res = $sql->buildSqlString($select);
        
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
     * Return array of category ids
     *
     * @param int $i
     * @param array $echo
     * @return array
     */
    public function findCategoryTree($i = '0', $echo = [])
    {
        $sql = new Sql($this->db);
        $select = $sql->select();
        $select->from($this->tableName);
        $select->where(['parent_id' => $i]);

//      Do not delete the following line
//      $selectString = $sql->buildSqlString($select);  exit(date("r")."<hr>".$selectString);

        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        foreach ($results as $result) {
            $echo[] = $result['id'];
            $echo = $this->findCategoryTree($result['id'], $echo);
        }
        return $echo;
    }

    /**
     * Return array of arrays [category id, category title]
     *
     * @param int $i
     * @param array $echo
     * @return array
     */
    public function findAllMatherCategories($i = 0, $echo = [])
    {
        $sql = new Sql($this->db);
        $select = $sql->select();
        $select->from($this->tableName);
        $select->where(['id' => $i]);

//      Do not delete the following line
//      $selectString = $sql->buildSqlString($select);  exit(date("r")."<hr>".$selectString);

        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        foreach ($results as $result) {
            $echo[] = [$result['id'], $result['title']];
            if ($result['parent_id']) {
                $echo = $this->findAllMatherCategories($result['parent_id'], $echo);
            }
            return $echo;
        }
    }

    /**
     * Return a single category.
     *
     * @param  array $params Identifier of the category to return.
     * @return Category
     */
    public function findCategory($params)
    {
        $sql = new Sql($this->db);
        $select = $sql->select($this->tableName);
        $select->where(['id = ?' => $params['id']]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                                    'Failed retrieving test with identifier "%s"; unknown database error.',
                                    $params['id']
            ));
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($result);
        $category = $resultSet->current();

        if (!$category) {
            throw new InvalidArgumentException(sprintf(
                                    'Category with identifier "%s" not found.',
                                    $params['id']
            ));
        }

        return $category;
    }

    /**
     * Adds given category into it's repository
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
        
//        $tableName = $this->tableName;
//        $this->mclient->saychas->$tableName->drop();
//        $this->mclient->saychas->$tableName->insertMany($result['data']);

        if ((bool) $result['truncate']) {
            $this->db->query("truncate table category")->execute();
        }

        foreach ($result['data'] as $row) {
            $sql = sprintf("replace INTO `category`(`title`, `parent_id`, `description`, `id`, `icon`, `sort_order`) VALUES ( '%s', '%s', '%s', '%s', '%s', %u)",
                    $row['title'], empty($row['parent_id']) ? '0' : $row['parent_id'], $row['description'], $row['id'], $row['icon'], $row['sort_order']);
            try {
                $query = $this->db->query($sql);
                $query->execute();
            } catch (InvalidQueryException $e) {
                return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
            }
        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }

    /**
     * Delete categories specified by json array of objects
     * @param $json
     */
    public function delete($json)
    {
        try {
            $result = Json::decode($json, Json::TYPE_ARRAY);
        } catch (LaminasJsonRuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
        $total = [];
        foreach ($result as $item) {
            array_push($total, $item['id']);
        }
        $sql = new Sql($this->db);
        $delete = $sql->delete()->from('category')->where(['id' => $total]);

        $selectString = $sql->buildSqlString($delete);
        try {
            $this->db->query($selectString, $this->db::QUERY_MODE_EXECUTE);
            return ['result' => true, 'description' => '', 'statusCode' => 200];
        } catch (InvalidQueryException $e) {
            return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
        }
    }

}
