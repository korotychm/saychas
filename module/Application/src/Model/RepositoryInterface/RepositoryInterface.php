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
    public function findAll();

    /**
     * Return a single application test.
     *
     * @param  int $id Identifier of the test to return.
     * @return Entity
     */
    public function find($id);
    
    /**
     * Return void
     * @param Entity
     */
    public function replace($entity);
    
}