<?php

// src/Model/Repository/CountryRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Application\Model\Entity\Country;
use Application\Model\RepositoryInterface\CountryRepositoryInterface;

class CountryRepository extends Repository implements CountryRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "country";

    /**
     * @var Country
     */
    protected Country $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Country $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            Country $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
    }

    /**
     * Adds given country into it's repository
     *
     * @param json
     */
    public function replace($content)
    {
        return ['result' => true, 'description' => '', 'statusCode' => 405];
    }

}
