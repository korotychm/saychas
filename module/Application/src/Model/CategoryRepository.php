<?php
// src/ModelCategoryRepository.php

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

use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Ddl;

//use Doctrine\ORM\Mapping as ORM;
//use Doctrine\Laminas\Hydrator\DoctrineObject as DoctrineHydrator;

//use Laminas\Db\ResultSet\ResultSet;

//use Laminas\Hydrator\ReflectionHydrator;

use Laminas\Db\Sql\Sql;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @var AdapterInterface
     */
    private AdapterInterface $db;

    /**
     * @var HydratorInterface
     */
    private HydratorInterface $hydrator;

    /**
     * @var Category
     */
    private Category $categoryPrototype;
    
    /**
     * @var string
     */
    private string $username;

    /**
     * 
     * @var string
     */
    private string $password;

    /**
     * @param Adapter $db
     * @param HydratorInterface $hydrator
     * @param Category $categoryPrototype
     * @param string $username
     * @param string $password
     */
    public function __construct(
//        AdapterInterface $db,
        AdapterInterface $db,
        HydratorInterface $hydrator,
        Category $categoryPrototype,
        string $username,
        string $password
    ) {
        $this->db            = $db;
        $this->hydrator      = $hydrator;
        $this->categoryPrototype = $categoryPrototype;
        $this->username = $username;
        $this->password = $password;
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
        /** TODO: to be fixed later */
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
        
        return str_replace("<ul></ul>","",$echo);
    }

    /**
     * Return a single category.
     *
     * @param  int $id Identifier of the category to return.
     * @return Category
     */    
    public function findCategory($id)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select('category');
        $select->where(['id_1c_group = ?' => $id]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                'Failed retrieving test with identifier "%s"; unknown database error.',
                $id
            ));
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->categoryPrototype);
        $resultSet->initialize($result); 
        $category = $resultSet->current();

        if (! $category) {
            throw new InvalidArgumentException(sprintf(
                'Category with identifier "%s" not found.',
                $id
            ));
        }

        return $category;
    }
    
    /**
     * Adds given array of categories into repository
     * @param array $data
     */
    public function addCategories(array $data)
    {
        //Таблица 1.6  Группы   (categories)
        //Id - id передаваемый из 1С,  лучше всего целое число
        //title - название группы, текст
        //parent – id  родительской группы из этой таблицы, 0 для корневых групп
        //description - описание, text/HTML
        //icon - целое число флаг для выбора иконки 
        //rang порядок отображения
        // [ [{"id": 1}, {"title": "title1"}, {"parent": null}, {"description": "description1"}, {"icon": 2}, {"rang": 2}], [{"id": 1}, {"title": "title1"}, {"parent": null}, {"description": "description1"}, {"icon": 2}, {"rang": 2}] ]
        
        // $sql       = new Sql($this->db);
        
        $categoryTable = new TableGateway('category', $this->db);
        
        $rowset = $categoryTable->select(['id_group' => 276643]);//['type' => 'PHP']
        
        print_r($rowset->current()['id_group']);

        // Existence of $adapter is assumed.
        //$sql = new Sql($this->db);
        
//        $query = $this->db->query('truncate table category');
//        $query->execute();

//        $this->db->query(
//            $sql->getSqlStringForSqlObject("TRUNCATE table category"),
//            $this->db::QUERY_MODE_EXECUTE
//        );
//        echo 'banzaii';
//        exit;
        
//        foreach($rowset as $row) {
//            print_r($row['group_name']);
//        }

//        echo '<pre>';
//        print_r($rowset);
//        echo '</pre>';
//        echo 'categories: ' . PHP_EOL;
//        foreach ($rowset as $categoryRow) {
//            echo $categoryRow['id'] . PHP_EOL;
//        }
//        
//        echo '201';
//        exit;

        foreach($data as $rows) {
            foreach($rows as $value) {
                echo "{$value['id']} {$value['title']} {$value['parent']} {$value['description']} {$value['icon']} {$value['rang']} <br/>";
            }
        }
        echo '200';
        exit;
    }
    
    
}