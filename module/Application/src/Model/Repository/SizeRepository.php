<?php

// src/Model/Repository/SizeRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Json\Json;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\Size;
use Application\Model\RepositoryInterface\SizeRepositoryInterface;

class SizeRepository extends Repository implements SizeRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "size";

    /**
     * @var Size
     */
    protected Size $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Size $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            Size $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
        
        parent::__construct();
    }

    /**
     * Adds given size into it's repository
     *
     * @param json
     */
    /** Old fashion code */
    /**
    public function replace($content)
    {
        try {
            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
        } catch (\Laminas\Json\Exception\RuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }

        foreach ($result['data'] as $row) {
            $sql = sprintf("replace INTO `size`(`id`, `title`) VALUES ( '%s', '%s')",
                    $row['id'], $row['title']);
            try {
                $query = $this->db->query($sql);
                $query->execute();
            } catch (InvalidQueryException $e) {
                return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
            }
        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }
    */

}
