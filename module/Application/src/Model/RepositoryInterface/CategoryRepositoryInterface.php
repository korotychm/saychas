<?php
namespace Application\Model\RepositoryInterface;

interface CategoryRepositoryInterface
{
    /**
     * Return a set of all application tests that we can iterate over.
     *
     * Each entry should be a Test instance.
     *
     * @return Category[]
     */
    public function findAllCategories($echo='', $i=0, $idActive);

    /**
     * Return a single application test.
     *
     * @param  int $id Identifier of the test to return.
     * @return Category
     */
    public function findCategory($id);
    
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