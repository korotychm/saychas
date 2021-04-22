<?php

// src/Model/Repository/RepositoryInterface/RepositoryInterface.php

namespace Application\Model\RepositoryInterface;

interface RepositoryInterface
{

    /**
     * Return a set of all entities that we can iterate over.
     *
     * Each entry should be an Entity instance.
     *
     * @return Entity[]
     */
    public function findAll(array $params);

    /**
     * Return a single entity or null if not found
     *
     * @param  array $params
     * @return Entity|null
     */
    public function find(array $params);

    /**
     * Return a single entity or default if not found
     *
     * @param array $params
     * @return Entity|default
     */
    public function findFirstOrDefault(array $params);

    /**
     * Return a single entity
     *
     * @param Entity
     * @return void
     */
    public function replace($entity);

    /**
     * Return void. Deletes entities specified by $json
     *
     * @param json
     * @return void
     */
    public function delete($json);
}
