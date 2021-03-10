<?php
namespace Application\Model;

interface RepositoryInterface
{
    /**
     * Return a set of all application tests that we can iterate over.
     *
     * Each entry should be a Test instance.
     *
     * @return Category[]
     */
    public function findAll();

    /**
     * Return a single application test.
     *
     * @param  int $id Identifier of the test to return.
     * @return Category
     */
    public function find($id);
    
    /**
     * Return void
     * @param Category[]
     */
    public function add($categories);
    
}