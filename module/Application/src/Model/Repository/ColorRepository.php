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
use Laminas\Hydrator\ClassMethodsHydrator;

class ColorRepository extends Repository implements ColorRepositoryInterface
{

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
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
    }

    /**
     * Adds given color into it's repository
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

        $hydrator = new ClassMethodsHydrator();
        foreach ($result['data'] as $row) {
            $hydrator->hydrate($row, $this->prototype);
            try{
                $this->persist($this->prototype, ['id' => $this->prototype->getId()]);
            }catch(Laminas\Db\Adapter\Exception\InvalidQueryException $e){
                return ['result' => false, 'description' => "error executing", 'statusCode' => 418];
            }
            /** Old fashion code */
//            $sql = sprintf("replace INTO `{$this->tableName}`(`id`, `title`, `value`) VALUES ( '%s', '%s', '%s')",
//                    $row['id'], $row['title'], $row['value']);
//            try {
//                $query = $this->db->query($sql);
//                $query->execute();
//            } catch (InvalidQueryException $e) {
//                return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
//            }
        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }

}
