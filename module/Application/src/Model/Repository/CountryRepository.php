<?php

// src/Model/Repository/CountryRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Json\Json;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
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
        try {
            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
        } catch (\Laminas\Json\Exception\RuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }

        if ((bool) $result['truncate']) {
            $this->db->query("truncate table {$this->tableName}")->execute();
        }

        foreach ($result['data'] as $row) {
            $sql = sprintf("replace INTO `{$this->tableName}`(`id`, `title`, `description`, `image`) VALUES ( '%s', '%s', '%s', '%s')",
                    $row['id'], $row['title'], $row['description'], $row['image']);
            try {
                $query = $this->db->query($sql);
                $query->execute();
            } catch (InvalidQueryException $e) {
                return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
            }
        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }

}
