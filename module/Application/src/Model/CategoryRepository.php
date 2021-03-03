<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model;

use InvalidArgumentException;
use RuntimeException;
// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Sql;

class CategoryRepository implements CategoryRepositoryInterface
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
    private $categoryPrototype;

    public function __construct(
//        AdapterInterface $db,
        Adapter $db,
        HydratorInterface $hydrator,
        Category $categoryPrototype
    ) {
        $this->db            = $db;
        $this->hydrator      = $hydrator;
        $this->categoryPrototype = $categoryPrototype;
    }

    /**
     * Return a set of all categories that we can iterate over.
     *
     * Each entry should be a Category instance.
     *
     * @return Category[]
     */
    public function findAllCategories1()
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select('category');
        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        return $result;

//        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
//            return [];
//        }
//
//        $resultSet = new HydratingResultSet($this->hydrator, $this->categoryPrototype);
//        $resultSet->initialize($result);
//        return $resultSet;
        
    }
    
    /**
     * Return a string that contains html ul list
     *
     * @return string
     */
    public function findAllCategories($echo = '', $i = 0, $idActive = false)
    {
        $sql = new Sql($this->db);
        $select = $sql->select();
        $select->from('category');
        $select->where(['parent' => $i ]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        
        if ($i) {
            $echo.="<ul>";
        }
        foreach($results as $result) {
            if (true /**|| pubtv(id_1C_group) */) // если в ветке есть хоть один товар, надо функцию сделать тоже такую
            {
                $groupName=stripslashes($result['group_name']);
                $echo.="<li><a href=#/catalog/".$result['id_1C_group']."  >$groupName</a>";
                        $echo=$this->findAllCategories($echo, $result['id_1C_group'],$idActive);
                $echo.="</li>";
            }
        }
        if ($i) {
            $echo .= "</ul>";
        }
        
        return $echo;
    }

    /**
     * Return a single category.
     *
     * @param  int $id Identifier of the category to return.
     * @return Category
     */
    
    public function findCategory($id)
    {
//        $sql       = new Sql($this->db);
//        $select    = $sql->select('category');
//        $select->where(['id = ?' => $id]);
//
//        $statement = $sql->prepareStatementForSqlObject($select);
//        $result    = $statement->execute();
//
//        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
//            throw new RuntimeException(sprintf(
//                'Failed retrieving test with identifier "%s"; unknown database error.',
//                $id
//            ));
//        }
//
//        $resultSet = new HydratingResultSet($this->hydrator, $this->postPrototype);
//        $resultSet->initialize($result);
//        $post = $resultSet->current();
//
//        if (! $post) {
//            throw new InvalidArgumentException(sprintf(
//                'Category with identifier "%s" not found.',
//                $id
//            ));
//        }
//
//        return $post;
    }    
}