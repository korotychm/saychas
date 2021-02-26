<?php
namespace Application\Model;

interface TestRepositoryInterface
{
    /**
     * Return a set of all application tests that we can iterate over.
     *
     * Each entry should be a Test instance.
     *
     * @return Test[]
     */
    public function findAllTests();

    /**
     * Return a single application test.
     *
     * @param  int $id Identifier of the test to return.
     * @return Test
     */
    public function findTest($id);
}