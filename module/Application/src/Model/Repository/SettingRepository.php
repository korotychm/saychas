<?php

// src/Model/Repository/SettingRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Json\Json;
//use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
//use Laminas\Db\Sql\Sql;
//use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\Setting;
use Application\Model\RepositoryInterface\SettingRepositoryInterface;

class SettingRepository extends Repository implements SettingRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "setting";

    /**
     * @var Setting
     */
    protected Setting $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Setting $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            Setting $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
    }

    /**
     * Adds given setting into it's repository
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
            // $this->db->query("truncate table {$this->tableName}")->execute();
            return ['result' => false, 'description' => 'option truncate unavailble', 'statusCode' => 405];
        }



        $data = $result['data'];

        $encodedData = Json::encode($data);

        $setting = new Setting();

        $setting->setId('main_menu');
        $setting->setValue($encodedData);

        $this->persist($setting, ['id' => $setting->getId()]);

        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }

    public function replaceNew($content)
    {
        try {
            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
        } catch (\Laminas\Json\Exception\RuntimeException $e) {
            return ['result' => false, 'error_description' => $e->getMessage(), 'statusCode' => 400];
        }

        if (empty($data = $result['data']) || !is_array($data)) {
            return ['result' => false, 'error_description' => 'data is empty', 'statusCode' => 400];
        }

        if ((bool) $result['truncate']) {
            return ['result' => false, 'error_description' => 'option truncate unavailble', 'statusCode' => 405];
        }

        while (list($key, $value) = each($data)) {
            $encodedData = Json::encode($value);
            $setting = new Setting();
            $setting->setId($key);
            $setting->setValue($encodedData)->persist(['id' => $key]);
        }

        return ['result' => true, 'error_description' => '', 'statusCode' => 200];
    }

}
