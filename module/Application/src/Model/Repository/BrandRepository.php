<?php

// src/Model/Repository/BrandRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Json\Json;
//use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
//use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\Brand;
use Application\Model\RepositoryInterface\BrandRepositoryInterface;

class BrandRepository extends Repository implements BrandRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "brand";

    /**
     * @var Brand
     */
    protected Brand $prototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Brand $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            Brand $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
        
        parent::__construct();
    }

    /**
     * Adds given brand into it's repository
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
            $sql = sprintf("replace INTO `{$this->tableName}`(`id`, `title`, `url`, `description`, `image`) VALUES ( '%s', '%s', '%s', '%s', '%s')",
                    $row['id'], addslashes($row['title']), addslashes($row['url']), addslashes($row['description']), $row['image']);
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
