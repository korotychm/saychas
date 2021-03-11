<?php
// src/ProviderRepository.php

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
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Sql;

class ProviderRepository implements ProviderRepositoryInterface
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
     * @var Provider
     */
    private Provider $providerPrototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Provider $providerPrototype
     */
    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        Provider $providerPrototype
    ) {
        $this->db            = $db;
        $this->hydrator      = $hydrator;
        $this->providerPrototype = $providerPrototype;
    }

    /**
     * Return a string that contains html ul list
     *
     * @return Provider[]
     */
    public function findAll()
    {
        return [];
    }

    /**
     * Return a single provider.
     *
     * @param  int $id Identifier of the provider to return.
     * @return Provider
     */    
    public function find($id)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select('provider');
        $select->where(['id = ?' => $id]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                'Failed retrieving test with identifier "%s"; unknown database error.',
                $id
            ));
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->providerPrototype);
        $resultSet->initialize($result);
        $provider = $resultSet->current();

        if (! $provider) {
            throw new InvalidArgumentException(sprintf(
                'Provider with identifier "%s" not found.',
                $id
            ));
        }

        return $provider;
    }
    
    /**
     * Adds given provider into repository
     * @param Provider
     */
    public function replace(Provider $provider)
    {
        echo 'replacing providers<br/>';
        print_r($provider);
        exit;
    }
    
    
}