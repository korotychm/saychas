<?php
namespace Application\Model;

interface ProviderRepositoryInterface
{
    /**
     * Return a set of all application tests that we can iterate over.
     *
     * Each entry should be a Test instance.
     *
     * @return Provider[]
     */
    public function findAll();

    /**
     * Return a single application test.
     *
     * @param  int $id Identifier of the test to return.
     * @return Provider
     */
    public function find($id);
    
    /**
     * @param Provider $provider
     */
    public function replace(Provider $provider);
}