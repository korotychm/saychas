<?php

// src/Model/Repository/ColorRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Json\Json;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\Color;
use Application\Model\RepositoryInterface\ColorRepositoryInterface;

class ColorRepository extends Repository implements ColorRepositoryInterface {

    /**
     * @var string
     */
    protected $tableName = "color";

    /**
     * @var Color
     */
    protected Color $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Color $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            Color $prototype
    ) {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
        
        parent::__construct();
    }

    /**
     * Adds given color into it's repository
     *
     * @param json
     */
    public function replace($content) {
        try {
            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
        } catch (\Laminas\Json\Exception\RuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }

        $this->mclient->saychas->color->drop();
        $this->mclient->saychas->color->insertMany($result->data);

        if ((bool) $result['truncate']) {
            $this->db->query("truncate table {$this->tableName}")->execute();
        }

        foreach ($result['data'] as $row) {
            $this->hydrator->hydrate($row, $this->prototype);
            try {
                $this->persist($this->prototype, ['id' => $this->prototype->getId()]);
            } catch (InvalidQueryException $e) {
                return ['result' => false, 'description' => "error executing", 'statusCode' => 418];
            }
        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }

}
