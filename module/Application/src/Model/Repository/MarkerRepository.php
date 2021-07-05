<?php

// src/Model/Repository/MarkerRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Json\Json;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\Marker;
use Application\Model\RepositoryInterface\MarkerRepositoryInterface;

class MarkerRepository extends Repository implements MarkerRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "marker";

    /**
     * @var Marker
     */
    protected Marker $prototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Marker $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            Marker $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
        
        parent::__construct();
    }

    /**
     * Adds given marker into it's repository
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

//        $tableName = $this->tableName;
//        $this->mclient->saychas->$tableName->drop();
//        $this->mclient->saychas->$tableName->insertMany($result['data']);

        if ((bool) $result['truncate']) {
            $this->db->query("truncate table {$this->tableName}")->execute();
        }

        foreach ($result['data'] as $row) {
            $sql = sprintf("replace INTO `{$this->tableName}`(`id`, `product_id`, `marker_index`) VALUES ('%s', '%s', %u)",
                    $row['product_id'], $row['product_id'], $row['marker_index']);
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
