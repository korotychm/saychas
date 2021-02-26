<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// In module/Application/src/Model/LaminasDbSqlRepository.php:
//namespace Application\Model;
//
//use InvalidArgumentException;
//use RuntimeException;
//use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Db\Sql\Sql;
//use Laminas\Db\Adapter\Driver\ResultInterface;
//use Laminas\Db\ResultSet\ResultSet;
//use Laminas\Hydrator\ReflectionHydrator;
//use Laminas\Db\ResultSet\HydratingResultSet;
//
//class LaminasDbSqlRepository implements TestRepositoryInterface
//{
//    /**
//     * @var AdapterInterface
//     */
//    private $db;
//
//    /**
//     * @param AdapterInterface $db
//     */
//    public function __construct(AdapterInterface $db)
//    {
//        $this->db = $db;
//    }
//    /**
//     * {@inheritDoc}
//     */
//    public function findAllTests()
//    {
//        $sql    = new Sql($this->db);
//        $select = $sql->select('test');
//        $stmt   = $sql->prepareStatementForSqlObject($select);
//        $result = $stmt->execute();
//        
//
//        $resultSet = new HydratingResultSet(
//            new ReflectionHydrator(),
//            new Post('', '')
//        );
//        
//        $resultSet->initialize($result);
//        
//        return $resultSet;
//    }
//
//    /**
//     * {@inheritDoc}
//     * @throws InvalidArgumentException
//     * @throws RuntimeException
//     */
//    public function findTest($id)
//    {
//    }
//}


// In module/Application/src/Model/LaminasDbSqlRepository.php:
namespace Application\Model;

use InvalidArgumentException;
use RuntimeException;
// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
//use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Sql;

class LaminasDbSqlRepository implements TestRepositoryInterface
{
    /**
     * @var AdapterInterface
     */
    private $db;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var Post
     */
    private $postPrototype;

    public function __construct(
//        AdapterInterface $db,
        Adapter $db,
        HydratorInterface $hydrator,
        Test $postPrototype
    ) {
        $this->db            = $db;
        $this->hydrator      = $hydrator;
        $this->postPrototype = $postPrototype;
    }

    /**
     * Return a set of all tests that we can iterate over.
     *
     * Each entry should be a Test instance.
     *
     * @return Test[]
     */
    public function findAllTests()
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select('test');
        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();

        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            return [];
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->postPrototype);
        $resultSet->initialize($result);
        return $resultSet;
    }

    /**
     * Return a single test.
     *
     * @param  int $id Identifier of the test to return.
     * @return Test
     */
//    public function findTest($id)
//    {
//    }
    
    public function findTest($id)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select('test');
        $select->where(['id = ?' => $id]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();

        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                'Failed retrieving test with identifier "%s"; unknown database error.',
                $id
            ));
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->postPrototype);
        $resultSet->initialize($result);
        $post = $resultSet->current();

        if (! $post) {
            throw new InvalidArgumentException(sprintf(
                'Test with identifier "%s" not found.',
                $id
            ));
        }

        return $post;
    }    
}