<?php
namespace Application\Model;

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
}