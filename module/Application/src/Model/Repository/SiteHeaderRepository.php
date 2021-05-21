<?php

// src/Model/Repository/SiteHeaderRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Json\Json;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\SiteHeader;
use Application\Model\RepositoryInterface\SiteHeaderRepositoryInterface;

class SiteHeaderRepository extends Repository implements SiteHeaderRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "site_header";

    /**
     * @var SiteHeader
     */
    protected SiteHeader $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param SiteHeader $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            SiteHeader $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
    }

    /**
     * Adds given site_header into it's repository
     *
     * @param json
     */
    public function replace($content)
    {
//        try {
//            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
//            print_r($result);
//        } catch (\Laminas\Json\Exception\RuntimeException $e) {
//            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
//        }
        $strategy = new \Laminas\Hydrator\Strategy\SerializableStrategy(
            new \Laminas\Serializer\Adapter\Json()
        );

        try {
            $hydrated = $strategy->hydrate($content);
        }catch(\Laminas\Json\Exception\RuntimeException $e){
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
        
        foreach($hydrated['data'][0] as $d) {
            print_r($d);
        }

        $siteHeader = new SiteHeader();
        $siteHeader->setId('000000002');
        $siteHeader->setCategoryId('000000002');
        $siteHeader->setTitle('title');
        $this->persist($siteHeader, ['category_id' => $siteHeader->getCategoryId()]);
//        try {
//            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
//        } catch (\Laminas\Json\Exception\RuntimeException $e) {
//            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
//        }
//
//        if ((bool) $result['truncate']) {
//            $this->db->query("truncate table {$this->tableName}")->execute();
//        }
//
//        foreach ($result['data'] as $row) {
//            $sql = sprintf("replace INTO `{$this->tableName}`(`id`, `title`, `description`, `image`) VALUES ( '%s', '%s', '%s', '%s')",
//                    $row['id'], $row['title'], $row['description'], $row['image']);
//            try {
//                $query = $this->db->query($sql);
//                $query->execute();
//            } catch (InvalidQueryException $e) {
//                return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
//            }
//        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }

}
