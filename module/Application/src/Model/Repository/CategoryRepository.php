<?php
// src/Model/Repository/CategoryRepository.php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
//use Laminas\Db\TableGateway\TableGateway;
use Application\Model\Entity\Category;
use Laminas\Db\Sql\Sql;

//use Doctrine\ORM\Mapping as ORM;
//use Doctrine\Laminas\Hydrator\DoctrineObject as DoctrineHydrator;

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
    public function findAllCategories($echo = '', $i = '0', $idActive = false)
    {
        $sql = new Sql($this->db);
        $select = $sql->select();
        $select->from('category');
        $select->where(['parent_id' => $i ]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        
        if ($i) {
            $echo.="<ul>";
        }
        /** TODO: to be fixed later */
        foreach($results as $result) {
            if (true /**|| pubtv(id_1C_group) */) // если в ветке есть хоть один товар, надо функцию сделать тоже такую
            {
                $groupName=stripslashes($result['title']);
                //$echo.="<li><a href=#/catalog/".$result['id_1C_group']."  >$groupName</a>";
                $echo.="<li><a href=#/catalog/".$result['id']."  >$groupName</a>";
                        $echo=$this->findAllCategories($echo, $result['id'],$idActive);
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
        $select->where(['id = ?' => $id]);

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
     * Adds given price into it's repository
     * 
     * @param json
     */
    public function replace($content)
    {
        try {
            $result = Json::decode($content, Laminas\Json\Json::TYPE_ARRAY);
        }catch(LaminasJsonRuntimeException $e){
           return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
         
        foreach($result as $row) {
            $sql = sprintf("replace INTO `category`(`title`, `parent_id`, `description`, `id`, `icon`, `sort_order`) VALUES ( '%s', '%s', '%s', '%s', '%s', %u)",
                    $row['title'], empty($row['parent_id']) ? '0' : $row['parent_id'], $row['description'], $row['id'], $row['icon'], $row['sort_order']);
            try {
                $query = $this->db->query($sql);
                $query->execute();
            }catch(InvalidQueryException $e){
                return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
            }
        }
        return ['result' => true, 'description' => ''];
    }
    
    /**
     * Delete categories specified by json array of objects
     * @param $json
     */
    public function delete($json) {
        /** @var id[] */
        return [];
    }

    
}