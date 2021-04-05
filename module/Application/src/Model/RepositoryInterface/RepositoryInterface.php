<?php
namespace Application\Model\RepositoryInterface;

interface RepositoryInterface
{
    /**
     * Return a set of all application tests that we can iterate over.
     *
     * Each entry should be a Test instance.
     *
     * @return Entity[]
     */
    public function findAll(array $params);

    /**
     * Return a single application test.
     *
     * @param  array $param
     * @return Entity
     */
    public function find(array $param);
    
    /**
     * Return void
     * @param Entity
     */
    public function replace($entity);
 
    /**
     * Return void
     * @param json
     */
    public function delete($json);
    
}